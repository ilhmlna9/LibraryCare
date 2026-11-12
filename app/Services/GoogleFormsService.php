<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintStatusHistory;
use App\Models\IntegrationSetting;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GoogleFormsService
{
    protected WhatsAppService $whatsAppService;
    protected TelegramService $telegramService;

    public function __construct(WhatsAppService $whatsAppService, TelegramService $telegramService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->telegramService = $telegramService;
    }

    public function getExpectedApiKey(): string
    {
        return IntegrationSetting::get('GOOGLE_FORMS_API_KEY', env('GOOGLE_FORMS_API_KEY', 'librarycare_secret_api_key'));
    }

    public function processSubmission(array $data, ?string $apiKey): array
    {
        $expectedKey = $this->getExpectedApiKey();

        if (empty($apiKey) || $apiKey !== $expectedKey) {
            return [
                'success' => false,
                'status_code' => 401,
                'message' => 'Autentikasi gagal. X-API-KEY tidak valid.',
            ];
        }

        $validator = Validator::make($data, [
            'visitor_name' => 'required|string|max:255',
            'visitor_email' => 'nullable|email|max:255',
            'visitor_phone' => 'nullable|string|max:25',
            'visitor_telegram' => 'nullable|string|max:100',
            'category_name' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:complaint_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'attachment_url' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'status_code' => 422,
                'message' => 'Validasi data formulir gagal.',
                'errors' => $validator->errors(),
            ];
        }

        $valid = $validator->validated();

        // Resolve Category
        $categoryId = null;
        if (!empty($valid['category_id'])) {
            $categoryId = $valid['category_id'];
        } elseif (!empty($valid['category_name'])) {
            $category = ComplaintCategory::where('name', 'LIKE', '%' . $valid['category_name'] . '%')
                ->orWhere('slug', Str::slug($valid['category_name']))
                ->first();
            if ($category) {
                $categoryId = $category->id;
            }
        }

        if (!$categoryId) {
            $defaultCategory = ComplaintCategory::firstOrCreate(
                ['slug' => 'kritik-saran-umum'],
                ['name' => 'Kritik & Saran Umum', 'icon' => 'sparkles', 'color' => 'purple', 'is_active' => true]
            );
            $categoryId = $defaultCategory->id;
        }

        $ticketNumber = Complaint::generateTicketNumber();

        $complaint = Complaint::create([
            'ticket_number' => $ticketNumber,
            'user_id' => null,
            'visitor_name' => $valid['visitor_name'],
            'visitor_email' => $valid['visitor_email'] ?? 'gforms@librarycare.local',
            'visitor_phone' => $valid['visitor_phone'] ?? null,
            'visitor_telegram' => $valid['visitor_telegram'] ?? null,
            'category_id' => $categoryId,
            'title' => $valid['title'],
            'description' => $valid['description'],
            'attachment' => $valid['attachment_url'] ?? null,
            'status' => 'pending',
            'source' => 'google_forms',
        ]);

        // History
        ComplaintStatusHistory::create([
            'complaint_id' => $complaint->id,
            'old_status' => null,
            'new_status' => 'pending',
            'notes' => 'Laporan masuk secara otomatis dari integrasi Google Forms & Google Sheets.',
        ]);

        // Log Notification
        NotificationLog::create([
            'complaint_id' => $complaint->id,
            'type' => 'google_forms',
            'recipient' => 'Sistem LibraryCare Webhook',
            'message' => "Pengaduan baru via Google Forms #{$ticketNumber} berhasil diproses.",
            'status' => 'sent',
            'response' => json_encode(['ticket_number' => $ticketNumber, 'received_at' => now()->toIso8601String()]),
        ]);

        // Send notifications
        if (!empty($complaint->visitor_telegram)) {
            $this->telegramService->notifyVisitorNewComplaint($complaint);
        }
        if (!empty($complaint->visitor_phone)) {
            $this->whatsAppService->sendTicketCreatedNotification($complaint);
        }

        return [
            'success' => true,
            'status_code' => 201,
            'message' => 'Laporan dari Google Forms berhasil diproses.',
            'ticket_number' => $complaint->ticket_number,
            'complaint_id' => $complaint->id,
            'status' => $complaint->status,
        ];
    }
}

