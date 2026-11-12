<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintReply;
use App\Models\ComplaintStatusHistory;
use App\Models\CustomerSatisfaction;
use App\Models\IntegrationSetting;
use App\Models\NotificationLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@librarycare.id'],
            [
                'name' => 'Administrator Perpustakaan',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'telegram_chat_id' => '123456789',
                'email_verified_at' => now(),
            ]
        );

        // 2. Visitors / Mahasiswa Users
        $visitor1 = User::firstOrCreate(
            ['email' => 'mahasiswa@librarycare.id'],
            [
                'name' => 'Ahmad Fauzi (Mahasiswa SI)',
                'password' => Hash::make('password123'),
                'role' => 'visitor',
                'phone' => '089876543210',
                'telegram_chat_id' => '987654321',
                'email_verified_at' => now(),
            ]
        );

        $visitor2 = User::firstOrCreate(
            ['email' => 'siti.aminah@librarycare.id'],
            [
                'name' => 'Siti Aminah (Mahasiswa TI)',
                'password' => Hash::make('password123'),
                'role' => 'visitor',
                'phone' => '081298765432',
                'telegram_chat_id' => null,
                'email_verified_at' => now(),
            ]
        );

        // 3. Complaint Categories
        $categoriesData = [
            [
                'name' => 'Fasilitas & Ruangan',
                'slug' => 'fasilitas-ruangan',
                'description' => 'Keluhan terkait AC, meja kursi, pencahayaan, kebersihan, atau kebisingan ruang baca.',
                'icon' => 'building-2',
                'color' => 'amber',
                'is_active' => true,
            ],
            [
                'name' => 'Koleksi Buku & E-Resource',
                'slug' => 'koleksi-buku-e-resource',
                'description' => 'Usulan buku baru, buku rusak/hilang, akses e-journal dan database online.',
                'icon' => 'book-open',
                'color' => 'indigo',
                'is_active' => true,
            ],
            [
                'name' => 'Layanan Sirkulasi & Denda',
                'slug' => 'layanan-sirkulasi-denda',
                'description' => 'Peminjaman, pengembalian, perpanjangan, kendala denda, dan kartu anggota.',
                'icon' => 'refresh-cw',
                'color' => 'teal',
                'is_active' => true,
            ],
            [
                'name' => 'WiFi, Komputer & IT',
                'slug' => 'wifi-komputer-it',
                'description' => 'Koneksi internet lambat, PC katalog eror, printer perpustakaan, atau website.',
                'icon' => 'wifi',
                'color' => 'sky',
                'is_active' => true,
            ],
            [
                'name' => 'Pelayanan Petugas',
                'slug' => 'pelayanan-petugas',
                'description' => 'Keramahan, kecepatan pelayanan, atau ketanggapan staf perpustakaan.',
                'icon' => 'users',
                'color' => 'rose',
                'is_active' => true,
            ],
            [
                'name' => 'Kritik & Saran Umum',
                'slug' => 'kritik-saran-umum',
                'description' => 'Ide perbaikan program, jam operasional perpustakaan, atau kegiatan literasi.',
                'icon' => 'sparkles',
                'color' => 'purple',
                'is_active' => true,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = ComplaintCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 4. Default Integration Settings
        $settings = [
            'WHATSAPP_API_URL' => 'https://graph.facebook.com/v19.0',
            'WHATSAPP_PHONE_NUMBER_ID' => '109876543210987',
            'WHATSAPP_ACCESS_TOKEN' => '',
            'WHATSAPP_SIMULATION_MODE' => 'true',
            'TELEGRAM_BOT_TOKEN' => '',
            'TELEGRAM_ADMIN_CHAT_ID' => '',
            'TELEGRAM_SIMULATION_MODE' => 'true',
            'GOOGLE_FORMS_API_KEY' => 'lc_gform_' . Str::random(24),
            'GOOGLE_FORMS_SIMULATION_MODE' => 'true',
            'LIBRARY_NAME' => 'Perpustakaan Utama Kampus',
            'LIBRARY_EMAIL' => 'library@university.ac.id',
            'LIBRARY_PHONE' => '+62 21 7890 1234',
            'LIBRARY_WHATSAPP' => '+62 812 3456 7890',
            'LIBRARY_TELEGRAM_BOT' => '@LibraryCareBot',
            'LIBRARY_ADDRESS' => 'Gedung Rektorat Lt. 2-3, Kampus Terpadu, Jl. Pendidikan No. 45',
            'LIBRARY_OPERATING_HOURS' => 'Senin - Jumat: 08.00 - 20.00 WIB | Sabtu: 08.30 - 15.00 WIB',
        ];

        foreach ($settings as $key => $val) {
            IntegrationSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $val, 'description' => 'Konfigurasi sistem untuk ' . strtolower(str_replace('_', ' ', $key))]
            );
        }

        // 5. Sample Complaints
        $samples = [
            [
                'ticket_number' => 'LIB-2026-0001',
                'user_id' => $visitor1->id,
                'visitor_name' => $visitor1->name,
                'visitor_email' => $visitor1->email,
                'visitor_phone' => $visitor1->phone,
                'category_id' => $categories['fasilitas-ruangan']->id,
                'title' => 'AC Ruang Baca Lantai 2 Kurang Dingin & Berisik',
                'description' => 'Mohon perhatian bagian sarpras, AC di sudut barat ruang baca lantai 2 mengeluarkan bunyi mendengung yang cukup bising dan hembusan anginnya kurang dingin, terutama pada siang hari saat ruangan penuh.',
                'status' => 'resolved',
                'source' => 'website',
                'admin_id' => $admin->id,
                'created_at' => Carbon::now()->subDays(6),
                'replies' => [
                    ['user_id' => $admin->id, 'message' => 'Halo Kak Ahmad, terima kasih laporannya. Teknisi pendingin ruangan sudah kami tugaskan untuk servis filter dan fan blower hari ini.', 'is_admin' => true],
                    ['user_id' => $visitor1->id, 'message' => 'Terima kasih banyak atas respon cepatnya!', 'is_admin' => false],
                    ['user_id' => $admin->id, 'message' => 'Servis AC telah selesai dilaksanakan dan suhu ruangan kembali sejuk optimal. Laporan kami tandai selesai.', 'is_admin' => true],
                ],
                'satisfaction' => ['rating' => 5, 'comment' => 'Penanganan sangat cepat dan ramah! Ruang baca sekarang nyaman kembali.'],
            ],
            [
                'ticket_number' => 'LIB-2026-0002',
                'user_id' => $visitor2->id,
                'visitor_name' => $visitor2->name,
                'visitor_email' => $visitor2->email,
                'visitor_phone' => $visitor2->phone,
                'category_id' => $categories['wifi-komputer-it']->id,
                'title' => 'Koneksi WiFi Library-Guest Sering Disconnect',
                'description' => 'Saat mengerjakan tugas akhir di kubikel riset, sinyal WiFi sering putus mendadak setiap 15 menit. Mohon dapat ditingkatkan stabilitasnya.',
                'status' => 'processing',
                'source' => 'whatsapp',
                'admin_id' => $admin->id,
                'created_at' => Carbon::now()->subDays(3),
                'replies' => [
                    ['user_id' => $admin->id, 'message' => 'Laporan kami terima via WhatsApp. Tim IT kampus sedang melakukan reboot Access Point di zona kubikel serta optimalisasi IP lease time.', 'is_admin' => true],
                ],
            ],
            [
                'ticket_number' => 'LIB-2026-0003',
                'user_id' => null,
                'visitor_name' => 'Budi Santoso',
                'visitor_email' => 'budisantoso99@gmail.com',
                'visitor_phone' => '085712345678',
                'category_id' => $categories['koleksi-buku-e-resource']->id,
                'title' => 'Usulan Penambahan Buku "Clean Architecture" Edisi Terbaru',
                'description' => 'Apakah perpustakaan bisa menyediakan edisi fisik maupun ebook untuk buku Clean Architecture karya Robert C. Martin? Buku ini sangat dibutuhkan untuk mata kuliah Rekayasa Perangkat Lunak.',
                'status' => 'pending',
                'source' => 'google_forms',
                'admin_id' => null,
                'created_at' => Carbon::now()->subHours(8),
                'replies' => [],
            ],
            [
                'ticket_number' => 'LIB-2026-0004',
                'user_id' => $visitor1->id,
                'visitor_name' => $visitor1->name,
                'visitor_email' => $visitor1->email,
                'visitor_phone' => $visitor1->phone,
                'category_id' => $categories['layanan-sirkulasi-denda']->id,
                'title' => 'Perpanjangan Mandiri via Website Mengalami Error 500',
                'description' => 'Saat mencoba klik tombol perpanjang masa pinjam buku "Database System Concepts", muncul notifikasi error. Mohon bantuan agar tidak terkena denda keterlambatan.',
                'status' => 'processing',
                'source' => 'telegram',
                'admin_id' => $admin->id,
                'created_at' => Carbon::now()->subHours(18),
                'replies' => [
                    ['user_id' => $admin->id, 'message' => 'Kami sudah bantu perpanjang manual selama 7 hari ke depan. Tidak ada denda yang dikenakan.', 'is_admin' => true],
                ],
            ],
            [
                'ticket_number' => 'LIB-2026-0005',
                'user_id' => null,
                'visitor_name' => 'Dewi Lestari',
                'visitor_email' => 'dewi.lestari@student.ac.id',
                'visitor_phone' => '087766554433',
                'category_id' => $categories['pelayanan-petugas']->id,
                'title' => 'Apresiasi Petugas Layanan Referensi Sangat Membantu',
                'description' => 'Saya ingin mengapresiasi Ibu Rina di meja referensi yang dengan sabar membimbing saya cara menelusuri jurnal internasional Scopus untuk tugas metodologi penelitian.',
                'status' => 'resolved',
                'source' => 'website',
                'admin_id' => $admin->id,
                'created_at' => Carbon::now()->subDays(12),
                'replies' => [
                    ['user_id' => $admin->id, 'message' => 'Terima kasih banyak Kak Dewi atas apresiasi hangatnya! Kami sudah sampaikan ke Bu Rina dan jajaran pimpinan perpustakaan.', 'is_admin' => true],
                ],
                'satisfaction' => ['rating' => 5, 'comment' => 'Pelayanan ramah dan inspiratif!'],
            ],
            [
                'ticket_number' => 'LIB-2026-0006',
                'user_id' => null,
                'visitor_name' => 'Rian Hidayat',
                'visitor_email' => 'rian.hidayat@yahoo.com',
                'visitor_phone' => '082133445566',
                'category_id' => $categories['kritik-saran-umum']->id,
                'title' => 'Usulan Buka Lebih Malam Selama Minggu Ujian Akhir',
                'description' => 'Bolehkah perpustakaan memperpanjang jam buka hingga pukul 22.00 WIB pada dua minggu masa UAS? Banyak mahasiswa yang membutuhkan tempat belajar kondusif bersama kelompok.',
                'status' => 'pending',
                'source' => 'website',
                'admin_id' => null,
                'created_at' => Carbon::now()->subDays(1),
                'replies' => [],
            ],
        ];

        foreach ($samples as $s) {
            $complaint = Complaint::firstOrCreate(
                ['ticket_number' => $s['ticket_number']],
                [
                    'user_id' => $s['user_id'],
                    'visitor_name' => $s['visitor_name'],
                    'visitor_email' => $s['visitor_email'],
                    'visitor_phone' => $s['visitor_phone'],
                    'category_id' => $s['category_id'],
                    'title' => $s['title'],
                    'description' => $s['description'],
                    'status' => $s['status'],
                    'source' => $s['source'],
                    'admin_id' => $s['admin_id'],
                    'created_at' => $s['created_at'],
                    'updated_at' => $s['created_at'],
                ]
            );

            // Status history
            ComplaintStatusHistory::firstOrCreate(
                [
                    'complaint_id' => $complaint->id,
                    'new_status' => 'pending',
                ],
                [
                    'old_status' => null,
                    'changed_by' => $s['user_id'],
                    'notes' => 'Laporan dibuat pertama kali melalui ' . ucfirst(str_replace('_', ' ', $s['source'])),
                    'created_at' => $s['created_at'],
                ]
            );

            if ($s['status'] === 'processing' || $s['status'] === 'resolved') {
                ComplaintStatusHistory::create([
                    'complaint_id' => $complaint->id,
                    'old_status' => 'pending',
                    'new_status' => 'processing',
                    'changed_by' => $admin->id,
                    'notes' => 'Laporan ditinjau dan sedang ditindaklanjuti oleh petugas.',
                    'created_at' => Carbon::parse($s['created_at'])->addHours(2),
                ]);
            }

            if ($s['status'] === 'resolved') {
                ComplaintStatusHistory::create([
                    'complaint_id' => $complaint->id,
                    'old_status' => 'processing',
                    'new_status' => 'resolved',
                    'changed_by' => $admin->id,
                    'notes' => 'Tindakan perbaikan selesai dan telah diverifikasi.',
                    'created_at' => Carbon::parse($s['created_at'])->addDays(1),
                ]);
            }

            // Replies
            foreach ($s['replies'] as $reply) {
                ComplaintReply::create([
                    'complaint_id' => $complaint->id,
                    'user_id' => $reply['user_id'],
                    'message' => $reply['message'],
                    'is_admin' => $reply['is_admin'],
                    'created_at' => Carbon::parse($s['created_at'])->addHours(rand(1, 10)),
                ]);
            }

            // Satisfaction
            if (isset($s['satisfaction'])) {
                CustomerSatisfaction::firstOrCreate(
                    ['complaint_id' => $complaint->id],
                    [
                        'user_id' => $s['user_id'],
                        'rating' => $s['satisfaction']['rating'],
                        'comment' => $s['satisfaction']['comment'],
                        'created_at' => Carbon::parse($s['created_at'])->addDays(2),
                    ]
                );
            }

            // Notification log
            NotificationLog::create([
                'complaint_id' => $complaint->id,
                'type' => $s['source'] === 'whatsapp' ? 'whatsapp' : ($s['source'] === 'telegram' ? 'telegram' : 'whatsapp'),
                'recipient' => $s['visitor_phone'] ?? '081234567890',
                'message' => "Halo {$s['visitor_name']}, tiket pengaduan perpustakaan Anda #{$s['ticket_number']} berhasil dicatat.",
                'status' => 'simulated',
                'response' => json_encode(['simulated' => true, 'timestamp' => now()->toISOString()]),
                'created_at' => $s['created_at'],
            ]);
        }
    }
}
