<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RekomendasiWisatawanController extends Controller
{
    public function index()
    {
        // Anda bisa menambahkan logika pengambilan data rekomendasi di sini
        return view('wisatawan.rekomendasi');
    }
}
