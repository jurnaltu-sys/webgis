<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RekomendasiWisatawanController extends Controller
{
    public function index()
    {
        // Ambil user yang sedang login
        $user = auth()->user();
        $email = $user ? $user->email : null;
        $userId = $user ? $user->id : null;

        // Ambil dataset ratting dari database
        $dataset = \App\Models\Ratting::with(['user', 'wisata'])->get();

        $users = [];
        $wisatas = [];
        $ratings = [];
        foreach ($dataset as $rat) {
            $users[$rat->user_id] = $rat->user->email;
            $wisatas[$rat->wisata_id] = $rat->wisata->nama;
            $ratings[$rat->user_id][$rat->wisata_id] = $rat->ratting;
        }
        $users = collect($users)->sortBy(function($email, $id) { return $id; })->all();

        // Hitung cosine similarity antar user
        $similarities = [];
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
            for ($i=0; $i<count($a); $i++) {
                $dot += $a[$i]*$b[$i];
                $normA += $a[$i]*$a[$i];
                $normB += $b[$i]*$b[$i];
            }
            $similarities[$otherId] = ($normA && $normB) ? $dot/(sqrt($normA)*sqrt($normB)) : 0;
        }

        // Cari user paling mirip
        $topUser = null;
        $topSimilarity = 0;
        foreach ($similarities as $uid => $sim) {
            if ($sim > $topSimilarity) {
                $topSimilarity = $sim;
                $topUser = $uid;
            }
        }
        $topUserEmail = $topUser ? ($users[$topUser] ?? '-') : '-';

        // Siapkan data untuk tabel: hanya user login dan user paling mirip
        $tabelUserIds = array_filter([$userId, $topUser]);
        $tabelUsers = collect($users)->only($tabelUserIds)->toArray(); // id=>email
        $wisataList = collect($wisatas)
            ->sort()
            ->map(function($nama, $id) { return ['id'=>$id,'nama'=>$nama]; })
            ->values()
            ->toArray(); // array of ['id'=>..,'nama'=>..]
        $pivot = [];
        foreach ($tabelUserIds as $uid) {
            foreach ($wisataList as $wisata) {
                $pivot[$uid][$wisata['id']] = $ratings[$uid][$wisata['id']] ?? 0;
            }
        }

        return view('wisatawan.rekomendasi', [
            'email' => $email,
            'topUserEmail' => $topUserEmail,
            'topSimilarity' => $topSimilarity,
            'users' => $tabelUsers,
            'wisataList' => $wisataList,
            'pivot' => $pivot,
        ]);
    }
}
