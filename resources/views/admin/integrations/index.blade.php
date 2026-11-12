@extends('layouts.admin')

@section('title', 'Pengaturan Integrasi API – WhatsApp, Telegram, Google Forms')
@section('header_title', 'Konfigurasi Integrasi & Saluran Komunikasi CRM')

@section('content')
<div class="space-y-8">
    <!-- Introduction Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-navy-900 to-teal-950 rounded-3xl p-6 sm:p-8 text-white border border-teal-800/40 shadow-md">
        <div class="max-w-3xl space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/20 text-teal-300 border border-teal-500/40">
                <i data-lucide="radio" class="w-3.5 h-3.5"></i>
                Multi-Channel Gateway
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Integrasi WhatsApp, Telegram & Google Forms</h2>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                Kelola kredensial API dan uji coba pengiriman pesan secara langsung. Saat token API belum tersedia, aktifkan <strong>Mode Simulasi</strong> agar pengujian lokal tetap berjalan lancar tanpa terhambat kredensial eksternal.
            </p>
        </div>
    </div>

    <!-- Integrations Settings Form -->
    <form action="{{ route('admin.integrations.update') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 1. WhatsApp Gateway Card (Fonnte / Cloud API) -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <i data-lucide="message-circle" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">WhatsApp Gateway Bot</h3>
                                <p class="text-[11px] text-slate-500">Notifikasi nomor pengunjung</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $settings['WHATSAPP_SIMULATION_MODE'] === 'true' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $settings['WHATSAPP_SIMULATION_MODE'] === 'true' ? 'Simulasi' : 'Live Gateway' }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Metode Provider WhatsApp</label>
                        <select name="whatsapp_gateway_provider" onchange="toggleWaProvider(this.value)" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500 font-semibold">
                            <option value="fonnte" {{ ($settings['WHATSAPP_GATEWAY_PROVIDER'] ?? 'fonnte') === 'fonnte' ? 'selected' : '' }}>📱 Fonnte (Scan QR No. HP Pribadi Tanpa Meta)</option>
                            <option value="cloud_api" {{ ($settings['WHATSAPP_GATEWAY_PROVIDER'] ?? '') === 'cloud_api' ? 'selected' : '' }}>🏢 WhatsApp Cloud API (Meta for Developers)</option>
                        </select>
                    </div>

                    <!-- Fonnte Fields -->
                    <div id="field-fonnte" class="{{ ($settings['WHATSAPP_GATEWAY_PROVIDER'] ?? 'fonnte') === 'fonnte' ? '' : 'hidden' }} space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Token Fonnte (Gratis / Free Plan)</label>
                            <input type="text" name="fonnte_token" value="{{ $settings['FONNTE_TOKEN'] ?? '' }}" placeholder="Contoh: a1b2c3d4e5f6g7h8..."
                                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white font-mono outline-none focus:ring-1 focus:ring-teal-500">
                            <p class="text-[10px] text-slate-400 mt-1">Dapatkan token di <a href="https://fonnte.com" target="_blank" class="text-teal-500 hover:underline">fonnte.com</a> lalu scan QR dengan nomor WA Anda.</p>
                        </div>
                    </div>

                    <!-- Meta Cloud API Fields -->
                    <div id="field-meta" class="{{ ($settings['WHATSAPP_GATEWAY_PROVIDER'] ?? '') === 'cloud_api' ? '' : 'hidden' }} space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number ID</label>
                            <input type="text" name="whatsapp_phone_number_id" value="{{ $settings['WHATSAPP_PHONE_NUMBER_ID'] }}" placeholder="109876543210987"
                                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white font-mono outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Permanent Access Token</label>
                            <input type="password" name="whatsapp_access_token" value="{{ $settings['WHATSAPP_ACCESS_TOKEN'] }}" placeholder="EAAG..."
                                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white font-mono outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Webhook URL WhatsApp (Fonnte / Meta)</label>
                        <input type="text" value="{{ url('/integration/whatsapp/webhook') }}" readonly
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-mono text-[11px] outline-none">
                    </div>

                    <div class="pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="whatsapp_simulation_mode" value="false">
                            <input type="checkbox" name="whatsapp_simulation_mode" value="true" {{ $settings['WHATSAPP_SIMULATION_MODE'] === 'true' ? 'checked' : '' }}
                                class="rounded border-slate-300 dark:border-slate-700 text-teal-600 focus:ring-teal-500">
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Aktifkan Mode Simulasi</span>
                        </label>
                        <p class="text-[11px] text-slate-500 ml-5 mt-0.5">Catat notifikasi ke database tanpa memanggil API pihak ke-3.</p>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modal-test-wa').classList.remove('hidden')" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold text-emerald-600 flex items-center justify-center gap-1.5 transition-colors">
                        <i data-lucide="send" class="w-3.5 h-3.5"></i>
                        <span>Uji Kirim Pesan WA</span>
                    </button>
                </div>
            </div>

            <!-- 2. Telegram Bot API Card -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/70 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                                <i data-lucide="send" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Telegram Bot API</h3>
                                <p class="text-[11px] text-slate-500">Peringatan admin & bot tiket</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $settings['TELEGRAM_SIMULATION_MODE'] === 'true' ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800' }}">
                            {{ $settings['TELEGRAM_SIMULATION_MODE'] === 'true' ? 'Simulasi' : 'Live API' }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Telegram Bot Token</label>
                        <input type="password" name="telegram_bot_token" value="{{ $settings['TELEGRAM_BOT_TOKEN'] }}" placeholder="123456789:ABCdefGhI..."
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white font-mono outline-none focus:ring-1 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Admin Chat ID / Group ID</label>
                        <input type="text" name="telegram_admin_chat_id" value="{{ $settings['TELEGRAM_ADMIN_CHAT_ID'] }}" placeholder="Contoh: 987654321 atau -1001234567"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white font-mono outline-none focus:ring-1 focus:ring-teal-500">
                        <p class="text-[10px] text-slate-400 mt-1">Tujuan notifikasi laporan baru untuk staf admin.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Webhook URL Telegram Bot</label>
                        <input type="text" value="{{ url('/integration/telegram/webhook') }}" readonly
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-mono text-[11px] outline-none">
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="telegram_simulation_mode" value="false">
                            <input type="checkbox" name="telegram_simulation_mode" value="true" {{ $settings['TELEGRAM_SIMULATION_MODE'] === 'true' ? 'checked' : '' }}
                                class="rounded border-slate-300 dark:border-slate-700 text-teal-600 focus:ring-teal-500">
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Aktifkan Mode Simulasi</span>
                        </label>
                        <p class="text-[11px] text-slate-500 ml-5 mt-0.5">Simulasikan pengiriman telegram tanpa memerlukan Bot Token asli.</p>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modal-test-telegram').classList.remove('hidden')" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold text-sky-600 flex items-center justify-center gap-1.5 transition-colors">
                        <i data-lucide="bell" class="w-3.5 h-3.5"></i>
                        <span>Uji Kirim Pesan Telegram</span>
                    </button>
                </div>
            </div>

            <!-- 3. Google Forms & Sheets Card -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-950/70 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                                <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Google Forms Webhook</h3>
                                <p class="text-[11px] text-slate-500">Integrasi Apps Script</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">
                            Aktif
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">X-API-KEY Autentikasi</label>
                        <div class="flex gap-2">
                            <input type="text" name="google_forms_api_key" value="{{ $settings['GOOGLE_FORMS_API_KEY'] }}" readonly
                                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-mono outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">URL Endpoint Webhook</label>
                        <input type="text" value="{{ $googleFormsWebhookUrl }}" readonly
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-mono text-[11px] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Link Eksternal Google Forms Anda (Opsional)</label>
                        <input type="url" name="google_forms_url" value="{{ $settings['GOOGLE_FORMS_URL'] ?? '' }}" placeholder="https://docs.google.com/forms/d/e/.../viewform"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-purple-500">
                        <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika ingin menggunakan Simulator Google Forms bawaan sistem.</p>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="google_forms_simulation_mode" value="false">
                            <input type="checkbox" name="google_forms_simulation_mode" value="true" {{ $settings['GOOGLE_FORMS_SIMULATION_MODE'] === 'true' ? 'checked' : '' }}
                                class="rounded border-slate-300 dark:border-slate-700 text-teal-600 focus:ring-teal-500">
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Mode Simulasi Uji Coba</span>
                        </label>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <button type="button" onclick="document.getElementById('modal-docs-gforms').classList.remove('hidden')" class="flex-1 py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold text-purple-600 flex items-center justify-center gap-1.5 transition-colors">
                        <i data-lucide="code" class="w-3.5 h-3.5"></i>
                        <span>Lihat Script & Panduan</span>
                    </button>
                    <form action="{{ route('admin.integrations.test.google-forms') }}" method="POST">
                        @csrf
                        <button type="submit" title="Simulasikan 1 Pengaduan dari Google Forms" class="py-2 px-3 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 hover:bg-purple-100 text-xs font-semibold">
                            Uji Post
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-slate-900 dark:bg-teal-600 hover:bg-slate-800 dark:hover:bg-teal-700 shadow-md text-xs flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Simpan Semua Perubahan Pengaturan</span>
            </button>
        </div>
    </form>

    <!-- Notification Logs Audit Table -->
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Riwayat Notifikasi Keluar (Notification Logs)</h3>
                <p class="text-xs text-slate-500">Log pengiriman WhatsApp, Telegram, dan Google Forms</p>
            </div>

            <!-- Log Type Filter -->
            <form action="{{ route('admin.integrations.index') }}" method="GET" class="flex items-center gap-2">
                <select name="type" onchange="this.form.submit()" class="py-1.5 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-navy-900 text-slate-800 dark:text-white">
                    <option value="">Semua Tipe</option>
                    <option value="whatsapp" {{ request('type') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="telegram" {{ request('type') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                    <option value="google_forms" {{ request('type') === 'google_forms' ? 'selected' : '' }}>Google Forms</option>
                </select>

                <select name="status" onchange="this.form.submit()" class="py-1.5 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-navy-900 text-slate-800 dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent (Live)</option>
                    <option value="simulated" {{ request('status') === 'simulated' ? 'selected' : '' }}>Simulated</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 dark:bg-navy-950 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Saluran</th>
                        <th class="py-3 px-4">Penerima</th>
                        <th class="py-3 px-4">Isi Pesan</th>
                        <th class="py-3 px-4">Status API</th>
                        <th class="py-3 px-4">Tiket Terkait</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3 px-4 uppercase font-bold text-slate-800 dark:text-white">
                                {{ $log->type }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-700 dark:text-slate-300">
                                {{ $log->recipient }}
                            </td>
                            <td class="py-3 px-4 max-w-sm truncate text-slate-600 dark:text-slate-400">
                                {{ $log->message }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $log->status === 'sent' ? 'bg-emerald-100 text-emerald-800' : ($log->status === 'simulated' ? 'bg-purple-100 text-purple-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono font-semibold text-teal-600 dark:text-teal-400">
                                @if($log->complaint)
                                    <a href="{{ route('admin.complaints.show', $log->complaint->id) }}" class="hover:underline">
                                        {{ $log->complaint->ticket_number }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                Belum ada catatan notifikasi pada kriteria ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Test WhatsApp -->
<div id="modal-test-wa" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="message-circle" class="w-4 h-4 text-emerald-500"></i>
                <span>Uji Coba Pengiriman WhatsApp</span>
            </h3>
            <button onclick="document.getElementById('modal-test-wa').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('admin.integrations.test.whatsapp') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label for="wa_test_phone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp Tujuan</label>
                <input type="text" id="wa_test_phone" name="phone" required placeholder="081234567890" value="081234567890"
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none">
            </div>

            <div>
                <label for="wa_test_msg" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Isi Pesan Uji Coba</label>
                <textarea id="wa_test_msg" name="message" rows="3" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none">Halo! Ini adalah pesan uji coba integrasi WhatsApp Business Cloud API dari sistem LibraryCare Perpustakaan.</textarea>
            </div>

            <p class="text-[11px] text-slate-500">
                @if($settings['WHATSAPP_SIMULATION_MODE'] === 'true')
                    <span class="text-amber-500 font-semibold">&bull; Mode Simulasi Aktif:</span> Pesan akan dicatat ke riwayat log pengiriman tanpa mengirim WhatsApp sungguhan.
                @else
                    <span class="text-emerald-500 font-semibold">&bull; Live Cloud API Aktif:</span> Pesan akan dikirim langsung via Meta Graph API.
                @endif
            </p>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-test-wa').classList.add('hidden')" class="px-4 py-2 text-xs text-slate-600 dark:text-slate-300">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700">Kirim Uji Coba</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Test Telegram -->
<div id="modal-test-telegram" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="send" class="w-4 h-4 text-sky-500"></i>
                <span>Uji Coba Pengiriman Telegram</span>
            </h3>
            <button onclick="document.getElementById('modal-test-telegram').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('admin.integrations.test.telegram') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label for="tg_test_chat_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Target Chat ID</label>
                <input type="text" id="tg_test_chat_id" name="chat_id" required placeholder="123456789" value="{{ $settings['TELEGRAM_ADMIN_CHAT_ID'] ?: '123456789' }}"
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none">
            </div>

            <div>
                <label for="tg_test_msg" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Isi Pesan Uji Coba</label>
                <textarea id="tg_test_msg" name="message" rows="3" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none">🔔 [TEST] Uji coba notifikasi bot LibraryCare berhasil dijalankan!</textarea>
            </div>

            <p class="text-[11px] text-slate-500">
                @if($settings['TELEGRAM_SIMULATION_MODE'] === 'true')
                    <span class="text-amber-500 font-semibold">&bull; Mode Simulasi:</span> Pesan tercatat di database lokal.
                @else
                    <span class="text-sky-500 font-semibold">&bull; Live Bot API:</span> Pesan dikirim langsung ke Telegram bot.
                @endif
            </p>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-test-telegram').classList.add('hidden')" class="px-4 py-2 text-xs text-slate-600 dark:text-slate-300">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-sky-600 hover:bg-sky-700">Kirim Uji Coba</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Panduan Google Forms & Apps Script -->
<div id="modal-docs-gforms" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-navy-900 rounded-3xl p-6 sm:p-8 max-w-3xl w-full border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Panduan Integrasi Google Forms & Google Sheets</h3>
                <p class="text-xs text-slate-500">Hubungkan tanggapan form pengunjung secara otomatis ke LibraryCare</p>
            </div>
            <button onclick="document.getElementById('modal-docs-gforms').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="space-y-4 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
            <div class="p-3 bg-teal-50 dark:bg-teal-950/40 rounded-xl border border-teal-200 dark:border-teal-800">
                <p class="font-bold text-teal-800 dark:text-teal-300 mb-1">Langkah 1: Buat Google Forms & Buka Google Sheets Terkait</p>
                <p>Buat formulir dengan pertanyaan: <strong>Nama Lengkap</strong>, <strong>Email</strong>, <strong>Nomor WhatsApp</strong>, <strong>Kategori</strong>, <strong>Judul Laporan</strong>, dan <strong>Deskripsi</strong>. Pastikan tanggapan diarahkan ke Spreadsheet.</p>
            </div>

            <div class="p-3 bg-purple-50 dark:bg-purple-950/40 rounded-xl border border-purple-200 dark:border-purple-800">
                <p class="font-bold text-purple-800 dark:text-purple-300 mb-1">Langkah 2: Buka Apps Script</p>
                <p>Di Spreadsheet Google Sheets Anda, klik menu <strong>Extensions &rarr; Apps Script</strong>. Hapus isi file <code>Code.gs</code> dan salin kode berikut:</p>
            </div>

            <!-- Ready-to-copy Apps Script Code Block -->
            <div class="relative rounded-xl overflow-hidden bg-slate-950 text-slate-100 p-4 font-mono text-[11px] leading-relaxed">
                <pre class="overflow-x-auto whitespace-pre"><code>/**
 * Script Otomatisasi Google Sheets &rarr; LibraryCare CRM Webhook
 */
function onFormSubmit(e) {
  // Ganti sesuai URL sistem LibraryCare Anda
  var ENDPOINT_URL = "{{ $googleFormsWebhookUrl }}";
  var API_KEY = "{{ $settings['GOOGLE_FORMS_API_KEY'] }}";

  // Membaca baris respon terbaru dari formulir
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
  var lastRow = sheet.getLastRow();
  var rowData = sheet.getRange(lastRow, 1, 1, sheet.getLastColumn()).getValues()[0];

  // Sesuaikan indeks kolom (0 = Timestamp, 1 = Nama, 2 = Email, 3 = WA, 4 = Kategori, 5 = Judul, 6 = Deskripsi)
  var payload = {
    visitor_name: rowData[1],
    visitor_email: rowData[2],
    visitor_phone: rowData[3] ? String(rowData[3]) : "",
    category_name: rowData[4] ? String(rowData[4]) : "Kritik & Saran Umum",
    title: rowData[5] || "Pengaduan via Google Forms",
    description: rowData[6] || "-"
  };

  var options = {
    method: "post",
    contentType: "application/json",
    headers: {
      "X-API-KEY": API_KEY
    },
    payload: JSON.stringify(payload),
    muteHttpExceptions: true
  };

  try {
    var response = UrlFetchApp.fetch(ENDPOINT_URL, options);
    Logger.log("Response: " + response.getContentText());
  } catch (err) {
    Logger.log("Error Webhook: " + err.toString());
  }
}</code></pre>
            </div>

            <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl">
                <p class="font-bold text-slate-800 dark:text-white mb-1">Langkah 3: Pasang Trigger onFormSubmit</p>
                <p>Pada Apps Script, klik ikon jam <strong>Triggers (Pemicu)</strong> di sidebar kiri &rarr; Klik <strong>Add Trigger</strong> &rarr; Pilih fungsi <code>onFormSubmit</code> &rarr; Event type pilih <strong>On form submit</strong> &rarr; Simpan.</p>
            </div>

            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800">
                <p class="font-bold text-emerald-800 dark:text-emerald-300 mb-1">Langkah 4: Atur Pesan Konfirmasi Google Forms (Link Pelacakan Live)</p>
                <p class="mb-2">Agar pengunjung yang mengisi Google Forms mendapatkan tautan pelacakan langsung ke website live, buka Google Forms Anda &rarr; Klik tab <strong>Setelan (Settings)</strong> &rarr; Buka accordion <strong>Presentasi (Presentation)</strong> &rarr; Pada <strong>Pesan konfirmasi (Confirmation message)</strong>, klik <strong>Edit</strong> dan masukkan teks berikut:</p>
                <div class="p-3 bg-slate-950 rounded-lg text-emerald-300 font-mono text-[10.5px] select-all leading-relaxed">
                    Terima kasih atas laporan Anda!<br><br>
                    Keluhan Anda telah otomatis diteruskan ke sistem CRM LibraryCare.<br>
                    Nomor tiket resmi pelacakan telah kami kirimkan langsung ke nomor WhatsApp Anda.<br><br>
                    Pantau progres laporan Anda di website:<br>
                    <span class="text-white underline">{{ url('/track') }}</span>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
            <button onclick="document.getElementById('modal-docs-gforms').classList.add('hidden')" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 dark:bg-teal-600 hover:bg-slate-800">
                Tutup Panduan
            </button>
        </div>
    </div>
</div>

<script>
    function toggleWaProvider(provider) {
        const fieldFonnte = document.getElementById('field-fonnte');
        const fieldMeta = document.getElementById('field-meta');
        if (provider === 'fonnte') {
            fieldFonnte.classList.remove('hidden');
            fieldMeta.classList.add('hidden');
        } else {
            fieldFonnte.classList.add('hidden');
            fieldMeta.classList.remove('hidden');
        }
    }
</script>
@endsection

