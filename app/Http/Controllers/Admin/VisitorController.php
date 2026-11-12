<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'visitor')
            ->withCount('complaints');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $visitors = $query->latest()->paginate(15)->withQueryString();

        return view('admin.visitors.index', compact('visitors'));
    }

    public function show($id)
    {
        $visitor = User::where('role', 'visitor')
            ->withCount('complaints')
            ->with(['complaints.category', 'complaints.satisfaction'])
            ->findOrFail($id);

        return view('admin.visitors.show', compact('visitor'));
    }
}

