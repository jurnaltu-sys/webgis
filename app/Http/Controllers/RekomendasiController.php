<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class RekomendasiController extends Controller
{
    public function proses(Request $request)
    {
        // Ambil user yang sedang login
        $user = Auth::user();
        $email = $user ? $user->email : null;
        $userId = $user ? $user->id : null;
        // Hapus semua data rekomendasi user ini sebelum menyimpan yang baru
        if ($userId) {
            \App\Models\Rekomendasi::where('id_user', $userId)->delete();
        }

        // Ambil dataset ratting dari database
        $dataset = \App\Models\Ratting::with(['user', 'wisata'])->get();

        // Implementasi Collaborative Filtering User-Based Cosine Similarity
        $userId = $user ? $user->id : null;
        $users = [];
        $wisatas = [];
        $ratings = [];

        // Susun matriks user-item
        foreach ($dataset as $rat) {
            $users[$rat->user_id] = $rat->user->email;
            $wisatas[$rat->wisata_id] = $rat->wisata->nama;
            $ratings[$rat->user_id][$rat->wisata_id] = $rat->ratting;
        }

        // Sort users by id ascending
        $users = collect($users)->sortBy(function($email, $id) { return $id; })->all();

        // Hitung cosine similarity antar user
        $similarities = [];
        $similarityDetails = [];
        foreach (array_keys($users) as $otherId) {
            if ($otherId == $userId) continue;
            $vecA = $ratings[$userId] ?? [];
            $vecB = $ratings[$otherId] ?? [];
            $allWisata = array_unique(array_merge(array_keys($vecA), array_keys($vecB)));
            $a = [];
            $b = [];
            foreach ($allWisata as $wid) {
                $a[] = $vecA[$wid] ?? 0;
                $b[] = $vecB[$wid] ?? 0;
            }
            $dot = 0; $normA = 0; $normB = 0;
            $dotDetail = [];
            $normADetail = [];
            $normBDetail = [];
            for ($i=0; $i<count($a); $i++) {
                $dot += $a[$i]*$b[$i];
                $dotDetail[] = $a[$i].'×'.$b[$i].'='.($a[$i]*$b[$i]);
                $normA += $a[$i]*$a[$i];
                $normADetail[] = $a[$i].'²='.($a[$i]*$a[$i]);
                $normB += $b[$i]*$b[$i];
                $normBDetail[] = $b[$i].'²='.($b[$i]*$b[$i]);
            }
            $similarities[$otherId] = ($normA && $normB) ? $dot/(sqrt($normA)*sqrt($normB)) : 0;
            $similarityDetails[$otherId] = [
                'a' => $a,
                'b' => $b,
                'dotDetail' => $dotDetail,
                'normADetail' => $normADetail,
                'normBDetail' => $normBDetail,
                'dot' => $dot,
                'normA' => $normA,
                'normB' => $normB,
                'similarity' => $similarities[$otherId],
            ];
        }

        // Cari user paling mirip (berdasarkan similarity tertinggi, tapi daftar similarity tetap urut email asc)
        $topUser = null;
        $topSimilarity = 0;
        foreach ($similarities as $uid => $sim) {
            if ($sim > $topSimilarity) {
                $topSimilarity = $sim;
                $topUser = $uid;
            }
        }
        $topUserEmail = $topUser ? ($users[$topUser] ?? '-') : '-';

        // Rekomendasikan wisata yang belum dinilai user, tapi dinilai user paling mirip
        $userRated = array_keys($ratings[$userId] ?? []);
        $topRated = $ratings[$topUser] ?? [];
        $rekomendasi = [];
        $rekomendasiToInsert = [];
        foreach ($topRated as $wid => $nilai) {
            if (!in_array($wid, $userRated) && $nilai >= 4) {
                $rekomendasi[] = $wisatas[$wid];
                $rekomendasiToInsert[] = [
                    'id_user' => $userId,
                    'id_wisata' => $wid,
                ];
            }
        }
        // Simpan ke tabel rekomendasi (hindari duplikat)
        if (!empty($rekomendasiToInsert)) {
            \App\Models\Rekomendasi::upsert($rekomendasiToInsert, ['id_user', 'id_wisata']);
        }

        // Kirim ke view rekomendasi
        return view('wisatawan.rekomendasi', [
            'rekomendasi' => $rekomendasi,
            'email' => $email,
            'topUserEmail' => $topUserEmail,
            'topSimilarity' => $topSimilarity,
            'similarities' => $similarities,
            'users' => $users,
            'userId' => $userId,
            'ratings' => $ratings,
            'similarityDetails' => $similarityDetails,
        ]);

        // Kirim ke view rekomendasi
        return view('wisatawan.rekomendasi', [
            'rekomendasi' => $rekomendasi,
            'email' => $email,
        ]);
    }
}
