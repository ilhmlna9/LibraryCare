<?php

namespace App\Console\Commands;

use App\Http\Controllers\IntegrationWebhookController;
use App\Models\IntegrationSetting;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TelegramPollCommand extends Command
{
    protected $signature = 'telegram:poll {--once : Jalankan satu kali saja}';
    protected $description = 'Worker Polling Pesan Telegram Bot LibraryCare';

    public function handle(IntegrationWebhookController $webhookController)
    {
        $token = IntegrationSetting::get('TELEGRAM_BOT_TOKEN', env('TELEGRAM_BOT_TOKEN'));

        if (empty($token) || $token === 'your_bot_token') {
            $this->error('Token Telegram Bot belum diatur.');
            return 1;
        }

        // Kunci proses tunggal (Single Instance Lock) agar jika ada terminal lain yang sengaja/tidak sengaja membuka worker, tidak akan terjadi dobel respon
        $lockFile = storage_path('framework/telegram_poll.lock');
        $fp = fopen($lockFile, 'c+');
        if (!$fp || !flock($fp, LOCK_EX | LOCK_NB)) {
            $this->warn('⚠️ Worker telegram:poll lain sudah aktif di sistem. Instance ini otomatis dihentikan agar tidak terjadi duplikasi balasan.');
            return 0;
        }

        $this->info("🤖 Worker Telegram Polling LibraryCare Aktif...");
        $this->info("Bot: @LibraryCare_bot");
        $this->info("Siap menerima /start, /help, /lapor, dan /status...");

        $offset = 0;

        while (true) {
            $url = "https://api.telegram.org/bot{$token}/getUpdates?offset={$offset}&timeout=10";
            
            // Gunakan curl.exe bawaan Windows yang terbukti stabil & lolos firewall
            $cmd = "curl.exe -s --max-time 15 \"{$url}\"";
            $output = shell_exec($cmd);

            if ($output) {
                $data = json_decode($output, true);

                if (!empty($data['ok']) && !empty($data['result'])) {
                    foreach ($data['result'] as $update) {
                        $updateId = $update['update_id'];
                        $offset = $updateId + 1;

                        $sender = $update['message']['from']['first_name'] ?? 'Pengunjung';
                        $chatId = $update['message']['chat']['id'] ?? '';
                        $text = $update['message']['text'] ?? '';

                        $this->line("📩 [Pesan Diterima] Dari: {$sender} (ID: {$chatId}) - Isi: \"{$text}\"");

                        // Kirimkan ke Webhook Controller internal
                        $req = new Request($update);
                        $webhookController->telegramWebhook($req);

                        $this->info("✅ [Diproses] Berhasil menindaklanjuti perintah dari {$sender}!");
                    }
                }
            }

            if ($this->option('once')) {
                break;
            }

            usleep(500000); // 0.5 detik
        }

        return 0;
    }
}
