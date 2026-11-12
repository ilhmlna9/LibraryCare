<x-mail::message>
# 🔔 Pembaruan Status Laporan Perpustakaan

Halo **{{ $complaint->visitor_name }}**,

Petugas perpustakaan telah memperbarui status penanganan untuk laporan pengaduan Anda:

<x-mail::panel>
**Kode Tiket:** `{{ $complaint->ticket_number }}`  
**Judul Laporan:** {{ $complaint->title }}  
**Status Terbaru:** **{{ match($newStatus) {
    'pending' => '⏳ Menunggu Peninjauan',
    'processing' => '⚙️ Sedang Diproses oleh Petugas',
    'resolved' => '✅ Selesai Ditangani',
    'rejected' => '❌ Ditolak / Tidak Valid',
    default => ucfirst($newStatus)
} }}**  

@if($notes)
**Catatan Petugas:**  
*{{ $notes }}*
@endif
</x-mail::panel>

@if($newStatus === 'resolved')
🎉 **Laporan Anda telah selesai ditangani dengan baik!** Terima kasih banyak atas kesabaran dan kerja sama Anda.  
Mohon luangkan waktu sejenak untuk memberikan ulasan dan penilaian bintang (rating kepuasan) atas pelayanan kami.
@endif

<x-mail::button :url="$trackUrl" color="primary">
{{ $newStatus === 'resolved' ? '⭐ Beri Penilaian & Lihat Detail' : '🔎 Pantau Linimasa Penanganan' }}
</x-mail::button>

Jika tombol di atas tidak dapat diklik, salin tautan berikut ke browser:  
[{{ $trackUrl }}]({{ $trackUrl }})

Salam hangat,  
**Tim Layanan & Manajemen Perpustakaan**  
*LibraryCare CRM System*
</x-mail::message>
