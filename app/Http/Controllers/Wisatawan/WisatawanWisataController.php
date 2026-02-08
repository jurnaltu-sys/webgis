<?php
namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Wisata;
use Illuminate\Http\Request;

class WisatawanWisataController extends Controller
{
    public function __construct()
    {
        // Pastikan hanya wisatawan yang bisa akses
        $this->middleware(function ($request, $next) {
            if (session('user_role') !== 'wisatawan') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $wisata = Wisata::all();
        return view('wisatawan.wisata.index', compact('wisata'));
    }

    public function show($id)
    {
        $wisata = Wisata::findOrFail($id);
        return view('wisatawan.wisata.show', compact('wisata'));
    }
}
