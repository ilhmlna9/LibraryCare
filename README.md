# LibraryCare – Sistem CRM Layanan Perpustakaan

> **Judul Project Tugas Akhir / Mata Kuliah:**  
> *"Rancang Bangun Sistem Customer Relationship Management (CRM) Berbasis Web untuk Pengelolaan Keluhan dan Saran Pengunjung Perpustakaan Terintegrasi WhatsApp, Telegram, dan Google Forms."*  
> Program Studi S1 Sistem Informasi.

---

## 📖 1. Tentang LibraryCare

**LibraryCare** adalah aplikasi Customer Relationship Management (CRM) berbasis web modern yang dirancang khusus untuk meningkatkan kualitas layanan perpustakaan perguruan tinggi. Aplikasi ini memusatkan seluruh kritik, keluhan fasilitas, dan usulan koleksi buku dari pengunjung melalui berbagai saluran (Formulir Website, WhatsApp, Telegram Bot, dan Google Forms) ke dalam satu dashboard admin terpadu yang transparan, terukur, dan akuntabel.

### 🌟 Fitur Utama

#### A. Pengunjung / Mahasiswa (Visitor)
1. **Landing Page Modern**: Desain minimalis dan profesional dengan penjelasan alur layanan, kartu kategori keluhan, FAQ, jam operasional perpustakaan, serta form interaktif.
2. **Formulir Pengaduan Multi-Media**: Pengunjung dapat mengisi nama, email, nomor WhatsApp, kategori, judul keluhan, deskripsi, dan mengunggah lampiran foto/dokumen (JPG, PNG, PDF maks. 5MB).
3. **Penerbitan Nomor Tiket Otomatis**: Format standar `LIB-YYYY-XXXX` (contoh: `LIB-2026-0001`) langsung digenerate begitu formulir berhasil dikirim.
4. **Pelacakan Tiket (Tracking Status)**: Pengunjung dapat melacak progres penanganan keluhan secara publik tanpa harus login.
5. **Portal Pengunjung Terdaftar**:
   - Pendaftaran & autentikasi akun mahasiswa.
   - Dashboard ringkasan pengaduan (Total, Menunggu, Diproses, Selesai).
   - Riwayat pengaduan dengan filter status dan pencarian.
   - Forum percakapan / diskusi dua arah dengan admin perpustakaan.
   - Penilaian kepuasan pengunjung (*Customer Satisfaction Rating* 1–5 bintang beserta ulasan).
   - Penautan akun Telegram dengan memasukkan Chat ID untuk menerima notifikasi otomatis dari bot.

#### B. Petugas / Administrator Perpustakaan (Admin)
1. **Dashboard Statistik Eksekutif**:
   - Kartu KPI ringkasan: Total Laporan, Menunggu, Diproses, Selesai Ditangani, Rata-rata Skor Kepuasan.
   - Grafik Interaktif Chart.js: Tren bulanan pengaduan (Line Chart) dan Distribusi berdasarkan Kategori (Doughnut Chart).
   - Bar progress distribusi sumber laporan (Website, WhatsApp, Telegram, Google Forms).
   - Tabel laporan terbaru dengan pencarian instan, filter status, filter kategori, dan pagination.
2. **Manajemen Laporan Pengaduan**:
   - Detail lengkap data pelapor, kategori, deskripsi, dan lampiran berkas.
   - Pengubahan status laporan: *Menunggu*, *Diproses*, *Selesai*, atau *Ditolak*.
   - Input catatan penanganan yang otomatis tercatat dalam linimasa riwayat status (*status histories*).
   - Pengiriman balasan resmi ke pengunjung (dengan opsi otomatis mengirim pesan WhatsApp).
   - Notifikasi otomatis terkirim ke WhatsApp pengunjung dan Telegram saat status laporan diperbarui.
3. **Manajemen Kategori Keluhan (CRUD)**:
   - Tambah, edit, dan hapus kategori pengaduan dengan ikon Lucide dan warna aksen.
   - Fitur toggle aktivasi/nonaktifkan kategori.
   - Perlindungan integritas referensi: kategori yang memiliki laporan tidak dapat dihapus sembarangan.
4. **Manajemen Data Pengunjung**:
   - Direktori seluruh pengguna terdaftar, status penautan Telegram, dan total tiket yang pernah diajukan.
   - Halaman detail profil dan seluruh histori pengaduan mahasiswa bersangkutan.
5. **Pengaturan Integrasi Multi-Channel**:
   - WhatsApp Business Cloud API (Phone Number ID, Access Token, Mode Simulasi).
   - Telegram Bot API (Bot Token, Admin Chat ID, Mode Simulasi, Tool uji coba pesan bot).
   - Google Forms & Sheets (Webhook URL, X-API-KEY Autentikasi, Generator token, dan Simulator pengaduan).
   - Audit trail log pengiriman notifikasi (*Notification Logs*).

#### C. Antarmuka UI/UX Modern & Aksesibilitas
- **Tema Dual Mode (Dark & Light Mode)**: Dukungan tema gelap navy/slate yang nyaman dan tema terang minimalis. Preferensi tema tersimpan di `localStorage` tanpa efek kedip (*flash*).
- **Desain Responsif**: Sangat nyaman diakses melalui smartphone, tablet, maupun monitor desktop.
- **Sidebar Admin Fleksibel**: Sidebar dapat diperkecil (*collapse*) dan memori status tersimpan.
- **Ikon & Animasi**: Menggunakan Lucide Icons dan efek scroll reveal halus dengan AOS (*respects prefers-reduced-motion*).

---

## 🛠️ 2. Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| **Backend** | Laravel 11 / 13 (PHP 8.2+ / PHP 8.5) – Arsitektur MVC |
| **Database** | MySQL (XAMPP / Laragon) |
| **Frontend** | Blade Templating, Tailwind CSS, JavaScript |
| **Ikon** | Lucide Icons |
| **Grafik** | Chart.js |
| **Animasi** | AOS (Animate On Scroll) & CSS Transition |
| **Integrasi 1** | WhatsApp Business Cloud API (Meta Graph API) |
| **Integrasi 2** | Telegram Bot API |
| **Integrasi 3** | Google Forms & Google Sheets via Google Apps Script Webhook |

---

## 🗄️ 3. Struktur Database

Tabel yang dibuat melalui migrasi resmi Laravel:
1. `users`: Menyimpan data akun Admin dan Pengunjung (mahasiswa), termasuk `role`, `phone`, dan `telegram_chat_id`.
2. `complaint_categories`: Master kategori pengaduan (Fasilitas, Sirkulasi, Koleksi Buku, IT/WiFi, Pelayanan Petugas, dll).
3. `complaints`: Tabel utama data keluhan dengan nomor tiket unik `LIB-YYYY-XXXX`, status, lampiran, dan kanal sumber.
4. `complaint_replies`: Percakapan dan tanggapan dua arah antara admin dan pengunjung.
5. `complaint_status_histories`: Pencatatan linimasa setiap perubahan status laporan beserta catatan admin.
6. `customer_satisfaction`: Skor bintang (1–5) dan ulasan pengunjung setelah laporan selesai.
7. `notification_logs`: Catatan riwayat seluruh notifikasi keluar (WhatsApp, Telegram, Webhook) beserta respon status API (*sent*, *simulated*, *failed*).
8. `integration_settings`: Konfigurasi token API dan parameter sistem yang dapat diubah langsung dari UI Admin.

---

## 🚀 4. Panduan Instalasi Lokal (XAMPP / Laragon)

### Prasyarat
- **PHP** versi 8.2 atau lebih tinggi (rekomendasi PHP 8.2 – 8.5).
- **Composer** versi 2.x.
- **MySQL / MariaDB** (dapat menggunakan XAMPP atau Laragon).
- **Web Server**: Apache XAMPP atau built-in `php artisan serve`.

### Langkah-langkah Instalasi

1. **Letakkan Project di direktori Web Server**:
   Jika menggunakan XAMPP:
   ```
   C:\xampp\htdocs\LibraryCare
   ```

2. **Pastikan MySQL XAMPP Berjalan**:
   Buka XAMPP Control Panel, lalu klik **Start** pada modul **MySQL** dan **Apache**.

3. **Konfigurasi File `.env`**:
   Buka file `.env` di folder project dan sesuaikan konfigurasi database:
   ```env
   APP_NAME=LibraryCare
   APP_ENV=local
   APP_KEY=base64:...
   APP_DEBUG=true
   APP_URL=http://localhost/LibraryCare/public

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=librarycare
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Jalankan Migrasi & Database Seeder**:
   Buka terminal/PowerShell di direktori `C:\xampp\htdocs\LibraryCare`, lalu jalankan:
   ```bash
   php artisan migrate --seed
   ```
   *Perintah ini akan membuat seluruh tabel dan mengisi data awal kategori, akun demo, serta contoh pengaduan.*

5. **Hubungkan Symlink Penyimpanan Berkas**:
   ```bash
   php artisan storage:link
   ```

6. **Menjalankan Aplikasi**:
   - **Opsi A (Melalui Apache XAMPP)**:
     Buka browser dan akses:
     ```
     http://localhost/LibraryCare/public
     ```
   - **Opsi B (Melalui Artisan Development Server)**:
     Jalankan:
     ```bash
     php artisan serve
     ```
     Lalu akses di browser: `http://127.0.0.1:8000`

---

## 🔑 5. Akun Demo untuk Pengujian

Pada halaman login (`/login`), sistem telah menyediakan tombol **Quick Fill** untuk mengisi akun uji coba secara instan:

| Role | Email | Password | Hak Akses |
|---|---|---|---|
| **Administrator** | `admin@librarycare.id` | `admin123` | Mengelola dashboard, seluruh keluhan, ubah status, balas pesan, kelola kategori, dan atur API. |
| **Pengunjung (Mahasiswa)** | `mahasiswa@librarycare.id` | `password123` | Melihat dashboard mahasiswa, kirim pengaduan, riwayat tiket, balas pesan admin, beri ulasan bintang. |

---

## 📱 6. Panduan Konfigurasi Integrasi API

### A. WhatsApp Business Cloud API
1. Masuk ke dashboard admin &rarr; menu **Integrasi API**.
2. Masukkan **Phone Number ID** dan **Access Token** dari Meta for Developers.
3. **Mode Simulasi**: Jika Anda belum memiliki akun Meta Cloud API berbayar, biarkan opsi **"Aktifkan Mode Simulasi"** tercentang. Sistem akan mencatat pesan ke tabel `notification_logs` dengan status `simulated` sehingga seluruh alur kerja CRM tetap dapat diuji dan didemonstrasikan 100% tanpa error.
4. Klik tombol **"Uji Kirim Pesan WA"** untuk mencoba simulator pengiriman pesan.

### B. Telegram Bot API
1. Buat bot Telegram baru melalui [@BotFather](https://t.me/BotFather) di Telegram untuk mendapatkan **Bot Token**.
2. Masukkan Bot Token dan **Admin Chat ID** pada menu Integrasi API.
3. Untuk pengunjung yang ingin menerima notifikasi:
   - Pengunjung membuka bot Telegram resmi perpustakaan dan mengetik `/start`.
   - Bot akan membalas dengan **Chat ID** pengunjung.
   - Pengunjung menyalin Chat ID tersebut ke menu **Profil Saya** di website LibraryCare.
4. Saat status laporan diubah oleh admin, notifikasi otomatis dikirimkan ke Telegram pengunjung.

### C. Google Forms & Google Sheets Webhook
LibraryCare menyediakan endpoint aman untuk menerima data dari Google Forms secara otomatis:
- **Endpoint Webhook**: `http://localhost/LibraryCare/public/integration/google-forms`
- **Header Autentikasi**: `X-API-KEY: {KUNCI_API_ANDA}`
- **Formulir Terintegrasi Langsung (Built-in Simulator)**: Akses langsung di `http://localhost/LibraryCare/public/google-forms`

Formulir ini dapat diakses langsung oleh pengunjung dan mahasiswa dari tombol **Google Forms** di landing page. Saat form disubmit, JavaScript client melakukan POST ke Webhook API dengan `X-API-KEY` dan langsung memunculkan tiket resmi secara real-time!

#### Cara Pemasangan di Google Sheets Nyata (Produksi):
1. Buat formulir di Google Forms dengan pertanyaan: *Nama Lengkap*, *Email*, *Nomor WhatsApp*, *Kategori*, *Judul Laporan*, dan *Deskripsi*.
2. Arahkan jawaban formulir ke Google Sheets.
3. Di Google Sheets, buka menu **Extensions &rarr; Apps Script**.
4. Masukkan kode Google Apps Script berikut:

```javascript
function onFormSubmit(e) {
  var ENDPOINT_URL = "http://YOUR_DOMAIN/integration/google-forms"; // Sesuaikan dengan domain / ngrok Anda
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
    headers: {
      "X-API-KEY": API_KEY
    },
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
5. Simpan dan tambahkan trigger pemicu `On form submit`.
6. Setiap kali ada pengisian Google Forms, sistem LibraryCare akan langsung membuat tiket pengaduan baru dan mengirimkan peringatan ke admin!

---

## 🧪 7. Pengujian Otomatis (Automated Testing)

Project ini dilengkapi dengan suite pengujian otomatis Feature Test untuk memastikan seluruh alur berjalan sempurna:
```bash
php artisan test --filter CrmSystemTest
```
Pengujian mencakup:
- Render landing page publik.
- Pengiriman pengaduan & generate nomor tiket `LIB-YYYY-XXXX`.
- Pelacakan tiket pengaduan.
- Login admin & pembaruan status keluhan.
- Percakapan dua arah (balasan admin & pengunjung).
- Akses dashboard pengunjung.
- Penerimaan webhook Google Forms dengan validasi API Key.
- Penanganan webhook Telegram.

---

## 👨‍💻 8. Pengembang

- **Nama**: Mahasiswa S1 Sistem Informasi
- **Mata Kuliah / Tugas**: Tugas Kuliah CRM / Proyek Akhir Sistem Informasi
- **Tahun**: 2026
