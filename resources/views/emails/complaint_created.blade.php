<x-mail::message>
# ✨ Terima Kasih Atas Pengaduan Anda

Halo **{{ $complaint->visitor_name }}**,

Laporan serta aspirasi Anda telah berhasil kami terima dan tercatat ke dalam sistem **LibraryCare** Layanan Perpustakaan.

<x-mail::panel>
**Kode Tiket Laporan Anda:**  
# `{{ $complaint->ticket_number }}`

**Judul Laporan:** {{ $complaint->title }}  
**Kategori:** {{ $complaint->category->name }}  
**Status Saat Ini:** ⏳ Menunggu Peninjauan Petugas  
**Waktu Diterima:** {{ $complaint->created_at->format('d M Y, H:i') }} WIB  
</x-mail::panel>

Laporan Anda telah diteruskan ke antrean petugas perpustakaan untuk ditindaklanjuti. Setiap pembaruan status akan kami kirimkan kembali secara otomatis ke alamat email ini.

Anda juga dapat memantau linimasa perkembangan laporan atau mengirimkan pesan langsung ke petugas melalui tombol di bawah ini:

<x-mail::button :url="$trackUrl" color="primary">
🔎 Lacak Status Pengaduan Saya
</x-mail::button>

Jika tombol di atas tidak dapat diklik, silakan salin tautan berikut ke browser Anda:  
[{{ $trackUrl }}]({{ $trackUrl }})

Terima kasih atas kepedulian Anda dalam membantu meningkatkan mutu fasilitas dan pelayanan perpustakaan.

Salam hangat,  
**Tim Layanan & Manajemen Perpustakaan**  
*LibraryCare CRM System*
</x-mail::message>
