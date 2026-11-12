# LibraryCare – Sistem CRM Layanan Perpustakaan Umum

> **Judul Project Tugas Akhir / Mata Kuliah:**  
> *"Rancang Bangun Sistem Customer Relationship Management (CRM) Berbasis Web untuk Pengelolaan Pengaduan dan Saran Pengunjung Perpustakaan Umum Terintegrasi WhatsApp, Telegram, dan Google Forms."*  
> Program Studi S1 Sistem Informasi.

---

## 📖 1. Tentang LibraryCare

**LibraryCare** adalah aplikasi Customer Relationship Management (CRM) berbasis web modern yang dirancang khusus untuk meningkatkan mutu pelayanan publik pada **Perpustakaan Umum**. Sistem ini memusatkan seluruh kritik, keluhan fasilitas gedung, kendala sirkulasi, serta usulan literasi dari masyarakat pemustaka melalui berbagai saluran digital (Formulir Website, WhatsApp Chatbot, Telegram Bot, dan Google Forms) ke dalam satu dashboard kerja admin terpadu yang transparan, terukur, dan akuntabel.

### 🌟 Karakteristik Utama Sistem:
1. **Layanan Terbuka Tanpa Beban Login (Zero Barrier)**: Seluruh masyarakat umum dan pemustaka dapat langsung menyampaikan aspirasi atau aduan tanpa kewajiban membuat akun atau login.
2. **Nomor Tiket Unik Resmi**: Setiap aduan otomatis memperoleh nomor tiket berstandar `LIB-YYYY-XXXX` (contoh: `LIB-2026-0001`).
3. **Pelacakan Terbuka (Public Tracking)**: Linimasa penanganan laporan dapat dipantau langsung oleh masyarakat secara transparan di halaman `/track` cukup dengan memasukkan nomor tiket.
4. **Portal Login Khusus Administrator**: Fitur autentikasi dan login di sistem murni diperuntukkan bagi Staf Petugas dan Administrator Perpustakaan untuk memverifikasi serta menindaklanjuti laporan.
5. **Evaluasi Kepuasan Pemustaka (CSAT)**: Mengukur kepuasan layanan dengan penilaian bintang 1–5 dan ulasan setelah perbaikan selesai.

---

## ⚖️ 2. Komparasi: LibraryCare vs Mengkritik Langsung / Kotak Saran Konvensional

| Parameter Pembanding | Mengkritik Langsung / Kotak Saran Manual | Sistem CRM Digital LibraryCare |
|---|---|---|
| **Kenyamanan & Keberanian Pelapor** | **Rendah.** Masyarakat sering sungkan, malu, atau canggung jika mengkritik petugas/fasilitas secara tatap muka. | **Tinggi.** Masyarakat dapat melapor secara nyaman dan aman dari smartphone/laptop kapan saja. |
| **Bukti & Dokumentasi Masalah** | Terbatas pada lisan atau tulisan tangan singkat tanpa bukti visual pendukung. | **Lengkap.** Mendukung lampiran foto kerusakan fasilitas atau berkas dokumen PDF hingga 5 MB. |
| **Transparansi Progres (Tracking)** | **Gelap (Nol).** Pelapor tidak pernah tahu apakah laporannya sedang dikerjakan atau diabaikan. | **Real-Time & Terbuka.** Setiap laporan memiliki Nomor Tiket resmi yang dapat dilacak linimasa pengerjaannya. |
| **Kemudahan Akses (Autentikasi)** | Harus datang langsung ke lokasi perpustakaan pada jam buka dinas. | **Sangat Mudah & Terbuka.** Tidak ada kewajiban login bagi masyarakat; laporan dapat dikirim 24 jam non-stop. |
| **Akuntabilitas Petugas (SLA)** | Sulit diawasi pimpinan. Penanganan masalah bergantung pada ingatan staf yang menerima. | **Tercatat di sistem:** siapa petugas yang memproses, tanggal/jam perubahan status, dan catatan kerjanya. |
| **Evaluasi & Analitik Manajemen** | Menghitung kertas saran secara manual satu per satu setiap akhir bulan. | **Otomatis & Real-Time** melalui grafik Chart.js, KPI metric, dan persentase kepuasan pengunjung. |

---

## 🛠️ 3. Teknologi yang Digunakan (Tech Stack)

| Komponen | Teknologi | Keterangan |
|---|---|---|
| **Backend Framework** | Laravel 11 / 13 | Arsitektur MVC, Eloquent ORM, Routing RESTful, CSRF Protection |
| **Bahasa Pemrograman** | PHP 8.2+ / PHP 8.4 | Typed Properties, Arrow Functions, High Performance Engine |
| **Database** | MySQL / MariaDB | Relasional ACID Compliant (Alwaysdata Cloud & XAMPP) |
| **Hosting Live** | Alwaysdata Cloud Hosting | Domain: `https://librarycare.alwaysdata.net` (Aktif 24 Jam) |
| **Frontend UI** | Blade & Tailwind CSS | Responsive Utilities, Lucide Icons, Mobile-First Design |
| **Tema Tampilan** | Dual Mode (Dark & Light) | Tema Gelap Navy & Terang Minimalis tersimpan di localStorage |
| **Visualisasi Data** | Chart.js | Grafik Line Tren Bulanan & Doughnut Sebaran Kategori |
| **Integrasi WhatsApp** | Fonnte Gateway API | Scan QR Nomor HP Pribadi/Dinas tanpa API Meta berbayar |
| **Integrasi Telegram** | Telegram Bot API | Bot interaktif `@LibraryCare_bot` dengan Webhook HTTPS |
| **Integrasi Formulir** | Google Forms & Apps Script | Webhook JSON otomatis dengan autentikasi `X-API-KEY` |

---

## 🔄 4. Alur Kerja Rinci Masing-Masing Kanal Pengaduan (Workflow Detail)

Sistem LibraryCare menyediakan 4 pintu masuk pengaduan yang masing-masing memiliki alur kerja teknis yang terstruktur dari awal hingga selesai:

### 4.1 Kanal 1: Formulir Web-Based (Website Resmi)
Alur pengaduan mandiri langsung melalui portal resmi perpustakaan:

1. **Akses & Pengisian Data:**
   - Pemustaka membuka website: `https://librarycare.alwaysdata.net/#form-pengaduan`.
   - Mengisi data formulir: **Nama Lengkap**, **Email** (opsional), **Nomor WhatsApp** (opsional kontak), **Kategori Masalah** (Fasilitas, Sirkulasi, Koleksi Buku, IT/WiFi, dll), **Judul Laporan**, dan **Deskripsi Masalah** (minimal 15 karakter).
   - Pengunjung dapat mengunggah bukti pendukung berupa foto kerusakan atau berkas PDF (maks. 5 MB).
2. **Pemrosesan Backend (Controller):**
   - Data dikirim melalui method `POST /complaints` ke `LandingController@storeComplaint`.
   - Request divalidasi keamanannya (CSRF token, tipe file MIME, ukuran berkas).
   - Berkas diunggah secara aman ke folder `storage/app/public/complaints`.
   - Sistem men-generate Nomor Tiket otomatis `LIB-YYYY-XXXX` melalui `Complaint::generateTicketNumber()`.
   - Data disimpan ke database MySQL tabel `complaints` dengan status awal `pending` dan `source = 'website'`.
   - Sistem mencatat histori awal ke tabel `complaint_status_histories`.
3. **Respon ke Pemustaka:**
   - Halaman browser pemustaka otomatis dialihkan (*redirect*) ke **Halaman Pelacakan Publik (`/track?ticket=LIB-2026-XXXX`)**.
   - Nomor tiket dan kartu linimasa langsung tampil jelas di layar tanpa pengunjung perlu login.
4. **Antrean & Tindak Lanjut Admin:**
   - Laporan baru langsung muncul di baris teratas **Dashboard Admin Perpustakaan** (`/admin/complaints`).
   - Petugas memverifikasi aduan, lalu mengubah status menjadi **Sedang Diproses (`processing`)** dan memasukkan catatan teknis (contoh: *"Teknisi jaringan sedang memeriksa router WiFi lantai 2"*).
5. **Penyelesaian & Penilaian Kepuasan (CSAT):**
   - Setelah pekerjaan fisik/administrasi selesai, admin mengubah status menjadi **Selesai (`resolved`)**.
   - Halaman pelacakan pemustaka membuka formulir bintang kepuasan (1–5 bintang) dan kolom ulasan layanan.

---

### 4.2 Kanal 2: WhatsApp Chatbot (Fonnte Gateway API)
Alur pengaduan instan menggunakan aplikasi pesan WhatsApp:

1. **Akses Pengunjung:**
   - Pemustaka membuka chat WhatsApp ke nomor resmi perpustakaan yang terhubung ke Fonnte Gateway.
   - Pemustaka dapat mengetik secara mandiri atau menekan tombol **"Pilih Topik & Chat WhatsApp"** di website yang otomatis membuatkan draft pesan.
2. **Format Pesan yang Didukung:**
   - **Pembuatan Laporan Baru:**
     ```text
     #LAPOR Lampu Ruang Baca Redup | Lampu meja baca nomor 12 di lantai 1 berkedip dan padam.
     ```
   - **Pengecekan Status Tiket:**
     ```text
     STATUS LIB-2026-0001
     ```
3. **Pemrosesan Webhook Masuk (Inbound Webhook):**
   - Pesan diterima oleh Fonnte Cloud dan diteruskan via HTTP POST ke:  
     `POST https://librarycare.alwaysdata.net/integration/whatsapp/webhook`
   - Controller `IntegrationWebhookController@whatsappWebhook` membedah parameter `sender` (nomor HP), `message` (isi teks), dan `name`.
   - **Sistem Pengaman Cerdas:** Sistem memfilter pesan. Jika bukan perintah `#LAPOR` atau `STATUS`, sistem diam (tidak mengganggu chat pribadi).
   - Jika perintah `#LAPOR`: sistem membuat tiket unik `LIB-YYYY-XXXX`, menyimpan data ke database tabel `complaints` dengan `source = 'whatsapp'`.
4. **Balasan Otomatis Bot WhatsApp:**
   - Bot seketika membalas chat WhatsApp pemustaka:
     > *"Halo Budi, Laporan keluhan Anda telah berhasil kami terima di sistem LibraryCare.*  
     > *🎫 Kode Tiket Anda: `LIB-2026-0005`*  
     > *Pantau linimasa progres di: https://librarycare.alwaysdata.net/track?ticket=LIB-2026-0005*  
     > *Atau balas ketik: STATUS LIB-2026-0005"*
5. **Tindak Lanjut & Notifikasi Pembaruan:**
   - Laporan masuk ke antrean admin dengan badge sumber *WhatsApp*.
   - Saat status diubah menjadi *Selesai* oleh admin, sistem dapat mengirim notifikasi WhatsApp pemberitahuan selesai beserta tautan ulasan kepuasan.

---

### 4.3 Kanal 3: Telegram Bot API (@LibraryCare_bot)
Alur pengaduan berbasis bot interaktif Telegram 24 jam:

1. **Akses Pengunjung:**
   - Pemustaka membuka aplikasi Telegram, mencari bot **`@LibraryCare_bot`** (`https://t.me/LibraryCare_bot`), lalu menekan tombol **START** (atau kirim `/start`).
   - Bot seketika menyapa nama pemustaka, mendeteksi Chat ID secara otomatis di latar belakang, dan memberikan menu panduan lengkap.
2. **Perintah Interaktif Bot:**
   - **Membuat Laporan Baru (`/lapor`):**
     ```text
     /lapor AC Lantai 2 Panas | Suhu ruangan di meja 5 terasa gerah tolong dicek
     ```
   - **Melacak Status Tiket (`/status`):**
     ```text
     /status LIB-2026-0001
     ```
   - **Menautkan Tiket dari Web (Deep Linking):**
     Pemustaka cukup mengeklik link `t.me/LibraryCare_bot?start=LIB-2026-0001` pada halaman web tracking, dan bot otomatis menautkan tiket tersebut ke chat Telegram pemustaka.
3. **Pemrosesan Webhook Telegram:**
   - Server Telegram meneruskan pesan ke webhook HTTPS:  
     `POST https://librarycare.alwaysdata.net/integration/telegram/webhook`
   - `IntegrationWebhookController@telegramWebhook` memproses pesan masuk.
   - Dilengkapi fitur **Debouncing Anti-Duplikasi** (mencegah duplikasi pelaporan jika Telegram mengirimkan retry request dalam 15 detik).
   - Sistem membuat tiket `LIB-YYYY-XXXX`, menyimpan Chat ID pemustaka ke kolom `visitor_telegram`, dan menyimpan laporan dengan `source = 'telegram'`.
4. **Respon Bot ke Pemustaka:**
   - Bot langsung membalas detik itu juga:
     > *"🎉 LAPORAN BERHASIL DIBUAT!*  
     > *🎫 Nomor Tiket Anda: `LIB-2026-0006`*  
     > *📌 Judul: AC Lantai 2 Panas*  
     > *🔄 Status: ⏳ Menunggu Peninjauan Petugas*  
     > *Setiap perkembangan penanganan akan otomatis dikirimkan ke chat ini!"*
5. **Notifikasi Otomatis ke Telegram Petugas & Pemustaka:**
   - Grup/Chat Admin menerima notifikasi bahwa tiket Telegram baru telah masuk.
   - Saat admin mengubah status tiket di panel web admin, bot Telegram **secara otomatis mengirim pesan pembaruan langsung ke chat room pemustaka** tanpa pemustaka perlu login sama sekali.

---

### 4.4 Kanal 4: Google Forms & Sheets (Apps Script Webhook)
Alur pengaduan menggunakan formulir online Google Forms / kuesioner tablet perpustakaan:

1. **Pengisian Google Forms:**
   - Pemustaka mengisi Google Forms resmi di smartphone atau tablet survei di lobi perpustakaan.
   - Pertanyaan mencakup: *Nama Lengkap*, *Email*, *Nomor WhatsApp*, *Kategori*, *Judul Laporan*, dan *Deskripsi Rinci*.
   - Pemustaka menekan tombol **Kirim (Submit)**.
2. **Otomatisasi Google Sheets & Apps Script:**
   - Tanggapan formulir otomatis masuk ke baris baru spreadsheet Google Sheets terkait.
   - Pemicu (*Trigger*) `On form submit` di Google Sheets memicu fungsi `onFormSubmit()` di Google Apps Script.
   - Script membaca baris data terbaru, mengonversinya menjadi payload JSON, dan menembak webhook HTTPS sistem LibraryCare:
     ```javascript
     UrlFetchApp.fetch("https://librarycare.alwaysdata.net/integration/google-forms", {
       method: "post",
       contentType: "application/json",
       headers: { "X-API-KEY": "KUNCI_API_DARI_ADMIN" },
       payload: JSON.stringify(payload)
     });
     ```
3. **Validasi & Pembuatan Tiket di Backend:**
   - Route `POST /integration/google-forms` menerima data.
   - `GoogleFormsService@processSubmission` memvalidasi header `X-API-KEY`.
   - Sistem mencocokkan nama kategori dan menerbitkan Nomor Tiket resmi `LIB-YYYY-XXXX` dengan `source = 'google_forms'`.
4. **Respon ke Pemustaka di Layar Google Forms:**
   - Di layar konfirmasi akhir Google Forms, pemustaka membaca pesan resmi yang telah disetel:
     > *"Terima kasih atas laporan Anda! Keluhan Anda telah otomatis diteruskan ke sistem CRM LibraryCare. Pantau progres laporan Anda di website: https://librarycare.alwaysdata.net/track"*
5. **Tindak Lanjut Admin:**
   - Tiket muncul di antrean kerja admin dengan label sumber *Google Forms*.
   - Petugas menindaklanjuti keluhan sesuai prosedur standar perpustakaan.

---

## 🗄️ 5. Struktur Database Relasional

Tabel-tabel utama yang dikelola melalui migrasi resmi Laravel:
1. `users`: Menyimpan kredensial login Administrator & Petugas Perpustakaan (`name`, `email`, `password` bcrypt, `role = 'admin'`).
2. `complaint_categories`: Master kategori pengaduan (Fasilitas Gedung, Layanan Sirkulasi, Koleksi Buku, IT/WiFi, Kebersihan, Pelayanan Petugas) beserta status aktif/nonaktif dan warna aksen.
3. `complaints`: Tabel transaksi utama penyimpan seluruh aduan dengan atribut `ticket_number` (unik), `visitor_name`, `visitor_email`, `visitor_phone`, `visitor_telegram`, `category_id`, `title`, `description`, `attachment`, `status` (*pending, processing, resolved, rejected*), dan `source` (*website, whatsapp, telegram, google_forms*).
4. `complaint_status_histories`: Pencatatan linimasa jejak audit (*audit trail*) setiap perubahan status laporan beserta nama staf pengubah dan catatan teknis.
5. `complaint_replies`: Log percakapan dan balasan resmi antara petugas dengan pelapor.
6. `customer_satisfaction`: Menyimpan penilaian bintang kepuasan pemustaka (1–5 bintang) beserta ulasan tertulis setelah tiket selesai.
7. `notification_logs`: Catatan riwayat audit seluruh notifikasi keluar (WhatsApp, Telegram, Webhook) beserta status API (*sent, simulated, failed*).
8. `integration_settings`: Konfigurasi dinamis token API (Fonnte, Telegram, Google Forms) dan jam operasional yang dapat diubah langsung dari UI Admin.

---

## 🔑 6. Akun Login Petugas (Khusus Administrator Perpustakaan)

Masyarakat umum dan pengunjung **tidak perlu login atau registrasi akun** (cukup mengisi formulir terbuka dan melacak progres via Nomor Tiket). Login hanya diperuntukkan bagi Administrator & Staf Perpustakaan:

| Role | Email | Password | Hak Akses |
|---|---|---|---|
| **Administrator** | `admin@librarycare.id` | `admin123` | Mengelola dashboard, seluruh keluhan pengaduan, ubah status penanganan, balas pesan, kelola master kategori, dan pengaturan API. |

---

## 🚀 7. Panduan Instalasi Lokal (XAMPP / Laragon)

### Prasyarat
- **PHP** versi 8.2 atau lebih tinggi (rekomendasi PHP 8.2 – 8.5).
- **Composer** versi 2.x.
- **MySQL / MariaDB** (melalui XAMPP atau Laragon).
- **Web Server**: Apache XAMPP atau built-in `php artisan serve`.

### Langkah-langkah Instalasi
1. Letakkan project di direktori web server:
   ```text
   C:\xampp\htdocs\LibraryCare
   ```
2. Nyalakan modul **Apache** dan **MySQL** di XAMPP Control Panel.
3. Konfigurasikan file `.env`:
   ```env
   APP_NAME=LibraryCare
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=librarycare
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. Jalankan migrasi dan data seeder:
   ```bash
   php artisan migrate --seed
   ```
5. Buat symlink penyimpanan berkas lampiran:
   ```bash
   php artisan storage:link
   ```
6. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
   Akses di browser melalui: `http://localhost:8000`.

---

## 📱 8. Konfigurasi Integrasi API Cloud (Produksi Live)

Domain Live Produksi: **`https://librarycare.alwaysdata.net`**

### A. WhatsApp Gateway Bot (Fonnte)
1. Buka dashboard admin &rarr; menu **Integrasi API**.
2. Masukkan **Token Fonnte** dari [fonnte.com](https://fonnte.com) setelah menghubungkan nomor WhatsApp via scan QR.
3. Atur Webhook di Fonnte ke: `https://librarycare.alwaysdata.net/integration/whatsapp/webhook`.
4. Pelapor dapat mengecek tiket kapan saja via WhatsApp: `STATUS LIB-2026-XXXX`.

### B. Telegram Bot API (@LibraryCare_bot)
1. Bot resmi: **[@LibraryCare_bot](https://t.me/LibraryCare_bot)**.
2. Webhook Telegram aktif terhubung ke:  
   `https://librarycare.alwaysdata.net/integration/telegram/webhook`.
3. Pengunjung dapat mengetik `/start`, `/lapor Judul | Keluhan`, atau `/status LIB-2026-XXXX`.

### C. Google Forms & Google Sheets Webhook
1. Di Google Sheets respon formulir Anda, buka **Extensions &rarr; Apps Script**.
2. Masukkan kode berikut:
```javascript
function onFormSubmit(e) {
  var ENDPOINT_URL = "https://librarycare.alwaysdata.net/integration/google-forms";
  var API_KEY = "KUNCI_API_DARI_DASHBOARD_ADMIN";

  var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
  var lastRow = sheet.getLastRow();
  var rowData = sheet.getRange(lastRow, 1, 1, sheet.getLastColumn()).getValues()[0];

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
    headers: { "X-API-KEY": API_KEY },
    payload: JSON.stringify(payload),
    muteHttpExceptions: true
  };

  try {
    var response = UrlFetchApp.fetch(ENDPOINT_URL, options);
    Logger.log("LibraryCare Response: " + response.getContentText());
  } catch (err) {
    Logger.log("Error Webhook: " + err.toString());
  }
}
```
3. Tambahkan trigger pemicu `On form submit`.
4. Di Google Forms &rarr; **Setelan** &rarr; **Presentasi** &rarr; **Pesan konfirmasi**, masukkan:
```text
Terima kasih atas laporan Anda!

Keluhan Anda telah otomatis diteruskan ke sistem CRM LibraryCare.
Nomor tiket resmi pelacakan telah kami kirimkan langsung ke nomor WhatsApp Anda.

Pantau progres laporan Anda di website:
https://librarycare.alwaysdata.net/track
```

---

## 🧪 9. Pengujian Otomatis (Automated Feature Testing)

Sistem telah diuji menggunakan Test Suite resmi Laravel:
```bash
php artisan test --filter CrmSystemTest
```
Pengujian mencakup:
- Render landing page publik.
- Pengiriman pengaduan & generate nomor tiket `LIB-YYYY-XXXX`.
- Pelacakan tiket pengaduan publik tanpa login.
- Login admin & pembaruan status keluhan.
- Percakapan dua arah (balasan admin).
- Penerimaan webhook Google Forms dengan validasi API Key.
- Penanganan webhook Telegram.

---

## 👨‍💻 10. Pengembang Sistem

- **Nama**: Mahasiswa S1 Sistem Informasi
- **Mata Kuliah / Tugas**: Tugas Kuliah CRM / Proyek Akhir Sistem Informasi
- **Tahun**: 2026
