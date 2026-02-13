<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wisata;
use App\Models\Kategori;
use App\Models\Ratting;

class DashboardAdminController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->input('q');
        $query = Wisata::query();
        if ($searchQuery) {
            $query->where('nama', 'like', "%{$searchQuery}%");
        }
        $searchResults = $query->with('foto')->get();
        $totalWisata = Wisata::count();
        $totalKategori = Kategori::count();
        $totalRattingSaya = Ratting::count();
        $avgRattingSaya = Ratting::avg('ratting');
        $totalUser = \App\Models\User::count();
        return view('admin.dashboard.index', [
            'searchQuery' => $searchQuery,
            'searchResults' => $searchResults,
            'totalWisata' => $totalWisata,
            'totalKategori' => $totalKategori,
            'totalRattingSaya' => $totalRattingSaya,
            'avgRattingSaya' => $avgRattingSaya,
            'totalUser' => $totalUser,
        ]);
    }
}
