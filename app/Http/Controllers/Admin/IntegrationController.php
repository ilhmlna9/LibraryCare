<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntegrationSetting;
use App\Models\NotificationLog;
use App\Services\GoogleFormsService;
use App\Services\TelegramService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IntegrationController extends Controller
{
    protected WhatsAppService $whatsAppService;
    protected TelegramService $telegramService;
    protected GoogleFormsService $googleFormsService;

    public function __construct(
        WhatsAppService $whatsAppService,
        TelegramService $telegramService,
        GoogleFormsService $googleFormsService
    ) {
        $this->whatsAppService = $whatsAppService;
        $this->telegramService = $telegramService;
        $this->googleFormsService = $googleFormsService;
    }

    public function index(Request $request)
    {
        $settings = [
            'WHATSAPP_GATEWAY_PROVIDER' => IntegrationSetting::get('WHATSAPP_GATEWAY_PROVIDER', 'fonnte'),
            'FONNTE_TOKEN' => IntegrationSetting::get('FONNTE_TOKEN', ''),
            'WHATSAPP_API_URL' => IntegrationSetting::get('WHATSAPP_API_URL', 'https://graph.facebook.com/v19.0'),
            'WHATSAPP_PHONE_NUMBER_ID' => IntegrationSetting::get('WHATSAPP_PHONE_NUMBER_ID', ''),
            'WHATSAPP_ACCESS_TOKEN' => IntegrationSetting::get('WHATSAPP_ACCESS_TOKEN', ''),
            'WHATSAPP_SIMULATION_MODE' => IntegrationSetting::get('WHATSAPP_SIMULATION_MODE', 'true'),
            'TELEGRAM_BOT_TOKEN' => IntegrationSetting::get('TELEGRAM_BOT_TOKEN', ''),
            'TELEGRAM_ADMIN_CHAT_ID' => IntegrationSetting::get('TELEGRAM_ADMIN_CHAT_ID', ''),
            'TELEGRAM_SIMULATION_MODE' => IntegrationSetting::get('TELEGRAM_SIMULATION_MODE', 'true'),
            'GOOGLE_FORMS_API_KEY' => IntegrationSetting::get('GOOGLE_FORMS_API_KEY', 'lc_gform_demo_key'),
            'GOOGLE_FORMS_URL' => IntegrationSetting::get('GOOGLE_FORMS_URL', ''),
            'GOOGLE_FORMS_SIMULATION_MODE' => IntegrationSetting::get('GOOGLE_FORMS_SIMULATION_MODE', 'true'),
        ];

        // Telegram Bot Info
        $botInfo = $this->telegramService->getBotInfo();

        // Notification Logs
        $logsQuery = NotificationLog::with('complaint');

        if ($request->filled('type')) {
            $logsQuery->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $logsQuery->where('status', $request->status);
        }

        $logs = $logsQuery->latest()->paginate(10)->withQueryString();

        $appUrl = url('/');
        $googleFormsWebhookUrl = route('integration.google-forms.webhook');

        return view('admin.integrations.index', compact('settings', 'botInfo', 'logs', 'appUrl', 'googleFormsWebhookUrl'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_gateway_provider' => 'nullable|string|in:fonnte,cloud_api',
            'fonnte_token' => 'nullable|string|max:200',
            'whatsapp_phone_number_id' => 'nullable|string|max:100',
            'whatsapp_access_token' => 'nullable|string|max:1000',
            'whatsapp_simulation_mode' => 'nullable|string|in:true,false',
            'telegram_bot_token' => 'nullable|string|max:200',
            'telegram_admin_chat_id' => 'nullable|string|max:100',
            'telegram_simulation_mode' => 'nullable|string|in:true,false',
            'google_forms_api_key' => 'nullable|string|max:100',
            'google_forms_url' => 'nullable|string|max:500',
            'google_forms_simulation_mode' => 'nullable|string|in:true,false',
        ]);

        if (isset($validated['whatsapp_gateway_provider'])) {
            IntegrationSetting::set('WHATSAPP_GATEWAY_PROVIDER', $validated['whatsapp_gateway_provider']);
        }
        if (isset($validated['fonnte_token'])) {
            IntegrationSetting::set('FONNTE_TOKEN', $validated['fonnte_token']);
        }
        if (isset($validated['whatsapp_phone_number_id'])) {
            IntegrationSetting::set('WHATSAPP_PHONE_NUMBER_ID', $validated['whatsapp_phone_number_id']);
        }
        if (isset($validated['whatsapp_access_token'])) {
            IntegrationSetting::set('WHATSAPP_ACCESS_TOKEN', $validated['whatsapp_access_token']);
        }
        IntegrationSetting::set('WHATSAPP_SIMULATION_MODE', $request->input('whatsapp_simulation_mode', 'false'));

        if (isset($validated['telegram_bot_token'])) {
            IntegrationSetting::set('TELEGRAM_BOT_TOKEN', $validated['telegram_bot_token']);
        }
        if (isset($validated['telegram_admin_chat_id'])) {
            IntegrationSetting::set('TELEGRAM_ADMIN_CHAT_ID', $validated['telegram_admin_chat_id']);
        }
        IntegrationSetting::set('TELEGRAM_SIMULATION_MODE', $request->input('telegram_simulation_mode', 'false'));

        if (isset($validated['google_forms_api_key'])) {
            IntegrationSetting::set('GOOGLE_FORMS_API_KEY', $validated['google_forms_api_key']);
        }
        if (isset($validated['google_forms_url'])) {
            IntegrationSetting::set('GOOGLE_FORMS_URL', $validated['google_forms_url']);
        }
        IntegrationSetting::set('GOOGLE_FORMS_SIMULATION_MODE', $request->input('google_forms_simulation_mode', 'false'));

        return back()->with('success', 'Pengaturan integrasi berhasil disimpan.');
    }

    public function regenerateGoogleFormsApiKey()
    {
        $newKey = 'lc_gform_' . Str::random(24);
        IntegrationSetting::set('GOOGLE_FORMS_API_KEY', $newKey, 'Kunci API integrasi Google Forms & Apps Script');

        return back()->with('success', 'API Key Google Forms berhasil di-generate ulang: ' . $newKey);
    }

    public function testWhatsApp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:8|max:20',
            'message' => 'required|string|min:3|max:500',
        ]);

        $service = new WhatsAppService();
        $result = $service->sendMessage($request->phone, $request->message);

        if ($result['success']) {
            $msg = $result['simulated'] 
                ? 'Uji coba WhatsApp tercatat berhasil dalam MODE SIMULASI. Lihat riwayat log di bawah.'
                : 'Pesan WhatsApp berhasil dikirim ke nomor tujuan via Cloud API.';
            return back()->with('success', $msg);
        }

        return back()->with('error', 'Gagal mengirim WhatsApp: ' . ($result['message'] ?? 'Error tidak diketahui'));
    }

    public function testTelegram(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|string|min:4|max:50',
            'message' => 'required|string|min:3|max:500',
        ]);

        $service = new TelegramService();
        $result = $service->sendMessage($request->chat_id, $request->message);

        if ($result['success']) {
            $msg = $result['simulated']
                ? 'Uji coba Telegram tercatat berhasil dalam MODE SIMULASI. Lihat riwayat log di bawah.'
                : 'Pesan Telegram berhasil dikirim ke Chat ID tujuan via Bot API.';
            return back()->with('success', $msg);
        }

        return back()->with('error', 'Gagal mengirim Telegram: ' . ($result['message'] ?? 'Error tidak diketahui'));
    }

    public function testGoogleFormsSimulation(Request $request)
    {
        $apiKey = $this->googleFormsService->getExpectedApiKey();

        $sampleData = [
            'visitor_name' => 'Pengunjung Simulasi ' . rand(10, 99),
            'visitor_email' => 'pengunjung' . rand(10, 99) . '@gmail.com',
            'visitor_phone' => '0812' . rand(10000000, 99999999),
            'category_name' => 'Fasilitas & Ruangan',
            'title' => 'Pengaduan Uji Integrasi Google Forms #' . rand(100, 999),
            'description' => 'Ini adalah data simulasi kiriman dari Google Apps Script untuk memastikan alur integrasi webhook Google Forms berjalan lancar.',
            'attachment_url' => 'https://drive.google.com/sample-file',
        ];

        $result = $this->googleFormsService->processSubmission($sampleData, $apiKey);

        if ($result['success']) {
            return back()->with('success', "Simulasi Google Forms berhasil! Tiket pengaduan baru telah dibuat: {$result['ticket_number']}");
        }

        return back()->with('error', 'Simulasi gagal: ' . ($result['message'] ?? 'Error'));
    }
}

