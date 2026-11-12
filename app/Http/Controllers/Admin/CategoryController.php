<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplaintCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ComplaintCategory::withCount('complaints')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:complaint_categories,name',
            'description' => 'nullable|string|max:500',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:30',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah terdaftar.',
            'icon.required' => 'Pilih ikon kategori.',
            'color.required' => 'Pilih warna kategori.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        ComplaintCategory::create($validated);

        return back()->with('success', "Kategori '{$validated['name']}' berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $category = ComplaintCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:complaint_categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:30',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return back()->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $category = ComplaintCategory::withCount('complaints')->findOrFail($id);

        if ($category->complaints_count > 0) {
            return back()->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena memiliki {$category->complaints_count} laporan terkait.");
        }

        $category->delete();

        return back()->with('success', "Kategori '{$category->name}' berhasil dihapus.");
    }

    public function toggle($id)
    {
        $category = ComplaintCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        $statusText = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kategori '{$category->name}' berhasil {$statusText}.");
    }
}

