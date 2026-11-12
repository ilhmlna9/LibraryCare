@extends('layouts.admin')

@section('title', 'Kategori Pengaduan Perpustakaan')
@section('header_title', 'Manajemen Kategori Keluhan & Saran')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-navy-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Klasifikasi Kategori Layanan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Atur bidang keluhan agar alur pengaduan terdistribusi dengan tepat</p>
        </div>
        <button onclick="document.getElementById('modal-add-category').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition-colors">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Tambah Kategori Baru</span>
        </button>
    </div>

    <!-- Category Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($categories as $cat)
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                            <i data-lucide="{{ $cat->icon ?: 'tag' }}" class="w-5 h-5"></i>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $cat->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $cat->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $cat->name }}</h3>
                        <p class="text-xs text-slate-500 font-mono mt-0.5">slug: {{ $cat->slug }}</p>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
                            {{ $cat->description ?: 'Tidak ada deskripsi.' }}
                        </p>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <span class="font-semibold text-slate-500">
                        {{ $cat->complaints_count }} laporan terkait
                    </span>

                    <div class="flex items-center gap-1.5">
                        <!-- Toggle Active -->
                        <form action="{{ route('admin.categories.toggle', $cat->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300" title="{{ $cat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <i data-lucide="{{ $cat->is_active ? 'eye-off' : 'eye' }}" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>

                        <!-- Edit Button (trigger modal) -->
                        <button onclick="openEditModal({{ json_encode($cat) }})" class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-teal-600" title="Edit Kategori">
                            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                        </button>

                        <!-- Delete Button -->
                        @if($cat->complaints_count == 0)
                            <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg border border-rose-200 dark:border-rose-900 hover:bg-rose-50 dark:hover:bg-rose-950 text-rose-600" title="Hapus Kategori">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white dark:bg-navy-900 rounded-2xl p-8 text-center text-slate-500 text-sm">
                Belum ada kategori yang dibuat.
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div id="modal-add-category" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 max-w-lg w-full border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Tambah Kategori Baru</h3>
            <button onclick="document.getElementById('modal-add-category').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="cat_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" id="cat_name" name="name" required placeholder="Contoh: Koleksi Jurnal Digital"
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="cat_icon" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Ikon Lucide <span class="text-rose-500">*</span></label>
                    <select id="cat_icon" name="icon" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none">
                        <option value="building-2">building-2 (Gedung/Ruangan)</option>
                        <option value="book-open">book-open (Buku/Koleksi)</option>
                        <option value="refresh-cw">refresh-cw (Sirkulasi/Pinjam)</option>
                        <option value="wifi">wifi (Internet/WiFi)</option>
                        <option value="users">users (Staf/Petugas)</option>
                        <option value="sparkles">sparkles (Saran/Inovasi)</option>
                        <option value="tag">tag (Umum)</option>
                    </select>
                </div>

                <div>
                    <label for="cat_color" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Warna Aksen <span class="text-rose-500">*</span></label>
                    <select id="cat_color" name="color" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none">
                        <option value="teal">Teal (Cyan)</option>
                        <option value="amber">Amber (Kuning/Oranye)</option>
                        <option value="indigo">Indigo (Biru Dongker)</option>
                        <option value="sky">Sky (Biru Muda)</option>
                        <option value="rose">Rose (Merah Muda)</option>
                        <option value="purple">Purple (Ungu)</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="cat_desc" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Ringkas</label>
                <textarea id="cat_desc" name="description" rows="3" placeholder="Jelaskan jenis masalah atau pengaduan yang masuk ke dalam kategori ini..."
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-add-category').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-sm">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="modal-edit-category" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 max-w-lg w-full border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Edit Kategori</h3>
            <button onclick="document.getElementById('modal-edit-category').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="form-edit-category" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" id="edit_name" name="name" required
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="edit_icon" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Ikon Lucide <span class="text-rose-500">*</span></label>
                    <select id="edit_icon" name="icon" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none">
                        <option value="building-2">building-2</option>
                        <option value="book-open">book-open</option>
                        <option value="refresh-cw">refresh-cw</option>
                        <option value="wifi">wifi</option>
                        <option value="users">users</option>
                        <option value="sparkles">sparkles</option>
                        <option value="tag">tag</option>
                    </select>
                </div>

                <div>
                    <label for="edit_color" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Warna Aksen <span class="text-rose-500">*</span></label>
                    <select id="edit_color" name="color" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none">
                        <option value="teal">Teal</option>
                        <option value="amber">Amber</option>
                        <option value="indigo">Indigo</option>
                        <option value="sky">Sky</option>
                        <option value="rose">Rose</option>
                        <option value="purple">Purple</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="edit_description" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Deskripsi</label>
                <textarea id="edit_description" name="description" rows="3"
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-edit-category').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-sm">
                    Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditModal(category) {
        document.getElementById('form-edit-category').action = `/admin/categories/${category.id}`;
        document.getElementById('edit_name').value = category.name;
        document.getElementById('edit_icon').value = category.icon;
        document.getElementById('edit_color').value = category.color;
        document.getElementById('edit_description').value = category.description || '';
        document.getElementById('modal-edit-category').classList.remove('hidden');
    }
</script>
@endpush

