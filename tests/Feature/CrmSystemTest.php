<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\IntegrationSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic category
        ComplaintCategory::create([
            'name' => 'Fasilitas & Ruangan',
            'slug' => 'fasilitas-ruangan',
            'description' => 'Kendala fasilitas',
            'icon' => 'building-2',
            'color' => 'amber',
            'is_active' => true,
        ]);

        IntegrationSetting::set('GOOGLE_FORMS_API_KEY', 'test_key_123');
    }

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('LibraryCare');
        $response->assertSee('Kirimkan Keluhan, Kritik, atau Saran');
    }

    public function test_public_user_can_submit_complaint_and_get_ticket(): void
    {
        $category = ComplaintCategory::first();

        $response = $this->post('/complaints', [
            'visitor_name' => 'Budi Pengunjung',
            'visitor_email' => 'budi@example.com',
            'visitor_phone' => '081234567890',
            'visitor_telegram' => '987654321',
            'category_id' => $category->id,
            'title' => 'AC Ruang Baca Panas',
            'description' => 'AC di ruang baca lantai 2 tidak dingin sejak pagi hari ini.',
        ]);

        $this->assertDatabaseHas('complaints', [
            'visitor_name' => 'Budi Pengunjung',
            'visitor_telegram' => '987654321',
            'title' => 'AC Ruang Baca Panas',
            'status' => 'pending',
        ]);

        $complaint = Complaint::where('visitor_email', 'budi@example.com')->first();
        $this->assertNotNull($complaint);
        $this->assertStringStartsWith('LIB-', $complaint->ticket_number);

        $response->assertRedirect(route('complaints.track', ['ticket' => $complaint->ticket_number]));
    }

    public function test_user_can_track_complaint_by_ticket(): void
    {
        $category = ComplaintCategory::first();
        $ticket = Complaint::generateTicketNumber();

        $complaint = Complaint::create([
            'ticket_number' => $ticket,
            'visitor_name' => 'Siti',
            'visitor_email' => 'siti@example.com',
            'category_id' => $category->id,
            'title' => 'Katalog Komputer Mati',
            'description' => 'Komputer katalog nomor 3 mati total saat dinyalakan.',
            'status' => 'pending',
            'source' => 'website',
        ]);

        $response = $this->get('/track?ticket=' . $ticket);
        $response->assertStatus(200);
        $response->assertSee($ticket);
        $response->assertSee('Katalog Komputer Mati');
    }

    public function test_admin_can_access_dashboard_and_update_status(): void
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $category = ComplaintCategory::first();
        $complaint = Complaint::create([
            'ticket_number' => 'LIB-2026-TEST',
            'visitor_name' => 'Mahasiswa Test',
            'visitor_email' => 'mhs@test.com',
            'visitor_phone' => '081234567890',
            'category_id' => $category->id,
            'title' => 'Uji Coba Laporan',
            'description' => 'Deskripsi laporan uji coba sistem perpustakaan.',
            'status' => 'pending',
        ]);

        // 1. Admin access dashboard
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('LIB-2026-TEST');

        // 2. Admin update status to processing
        $statusResponse = $this->actingAs($admin)->post("/admin/complaints/{$complaint->id}/status", [
            'status' => 'processing',
            'notes' => 'Petugas sedang menuju lokasi.',
        ]);

        $statusResponse->assertSessionHas('success');
        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'status' => 'processing',
        ]);

        $this->assertDatabaseHas('complaint_status_histories', [
            'complaint_id' => $complaint->id,
            'new_status' => 'processing',
            'notes' => 'Petugas sedang menuju lokasi.',
        ]);

        // 3. Admin post reply
        $replyResponse = $this->actingAs($admin)->post("/admin/complaints/{$complaint->id}/reply", [
            'message' => 'Laporan Anda sudah kami konfirmasi ke teknisi.',
            'send_whatsapp' => 1,
        ]);

        $replyResponse->assertSessionHas('success');
        $this->assertDatabaseHas('complaint_replies', [
            'complaint_id' => $complaint->id,
            'message' => 'Laporan Anda sudah kami konfirmasi ke teknisi.',
            'is_admin' => true,
        ]);
    }

    public function test_visitor_can_access_visitor_dashboard(): void
    {
        $visitor = User::create([
            'name' => 'Mahasiswa Pengunjung',
            'email' => 'visitor@test.com',
            'password' => bcrypt('password'),
            'role' => 'visitor',
        ]);

        $response = $this->actingAs($visitor)->get('/visitor/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Portal Pelayanan');
    }

    public function test_google_forms_webhook_creates_complaint(): void
    {
        $payload = [
            'visitor_name' => 'Pelapor Google Forms',
            'visitor_email' => 'gforms@student.ac.id',
            'visitor_phone' => '089988776655',
            'category_name' => 'Fasilitas & Ruangan',
            'title' => 'Lampu Ruang Diskusi Redup',
            'description' => 'Lampu di ruang diskusi kelompok lantai 3 berkedip-kedip.',
        ];

        $response = $this->postJson('/integration/google-forms', $payload, [
            'X-API-KEY' => 'test_key_123',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('complaints', [
            'visitor_name' => 'Pelapor Google Forms',
            'source' => 'google_forms',
            'status' => 'pending',
        ]);
    }

    public function test_google_forms_webhook_rejects_invalid_api_key(): void
    {
        $payload = [
            'visitor_name' => 'Hacker',
            'visitor_email' => 'fake@example.com',
            'title' => 'Fake Title',
            'description' => 'Fake description',
        ];

        $response = $this->postJson('/integration/google-forms', $payload, [
            'X-API-KEY' => 'wrong_key',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_telegram_webhook_responds_ok(): void
    {
        $update = [
            'update_id' => 123456,
            'message' => [
                'message_id' => 1,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Ahmad'],
                'text' => '/start',
            ],
        ];

        $response = $this->postJson('/integration/telegram/webhook', $update);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    public function test_google_forms_simulation_page_renders_successfully(): void
    {
        $response = $this->get('/google-forms');
        $response->assertStatus(200);
        $response->assertSee('Google Forms Webhook Integration');
        $response->assertSee('Formulir Aspirasi & Keluhan Pengunjung Perpustakaan', false);
    }

    public function test_telegram_lapor_command_creates_complaint(): void
    {
        $update = [
            'update_id' => 123457,
            'message' => [
                'message_id' => 2,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Budi'],
                'text' => '/lapor Kursi Rusak | Kursi di meja 12 lantai 2 patah satu kakinya.',
            ],
        ];

        $response = $this->postJson('/integration/telegram/webhook', $update);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);

        $this->assertDatabaseHas('complaints', [
            'visitor_name' => 'Budi',
            'title' => 'Kursi Rusak',
            'source' => 'telegram',
        ]);
    }

    public function test_whatsapp_webhook_lapor_message_creates_complaint(): void
    {
        $update = [
            'entry' => [
                [
                    'changes' => [
                        [
                            'value' => [
                                'contacts' => [
                                    ['profile' => ['name' => 'Citra Lestari'], 'wa_id' => '6281234567890']
                                ],
                                'messages' => [
                                    [
                                        'from' => '6281234567890',
                                        'id' => 'wamid.HBgLMjY...',
                                        'type' => 'text',
                                        'text' => ['body' => '#LAPOR Wi-Fi Lambat | Akses internet di sudut barat perpustakaan sering putus.']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/integration/whatsapp/webhook', $update);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);

        $this->assertDatabaseHas('complaints', [
            'visitor_name' => 'Citra Lestari',
            'visitor_phone' => '6281234567890',
            'title' => 'Wi-Fi Lambat',
            'source' => 'whatsapp',
        ]);
    }
}
