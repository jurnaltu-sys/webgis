<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Services\CollaborativeFilteringService;

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

        // Implementasi Collaborative Filtering User-Based dengan tahapan yang benar
        $service = new CollaborativeFilteringService();
        $userId = $user ? $user->id : null;
        $users = [];
        $wisatas = [];
        $ratings = [];

        // Susun matriks user-item (sparse: missing => null)
        foreach ($dataset as $rat) {
            $users[$rat->user_id] = $rat->user->email;
            $wisatas[$rat->wisata_id] = $rat->wisata->nama;
            $ratings[$rat->user_id][$rat->wisata_id] = $rat->ratting;
        }

        // Sort users by id ascending
        $users = collect($users)->sortBy(function($email, $id) { return $id; })->all();

        // Build full rating matrix with explicit nulls for missing
        $allWisataIds = array_keys($wisatas);
        $fullRatings = [];
        foreach (array_keys($users) as $uid) {
            $fullRatings[$uid] = [];
            foreach ($allWisataIds as $wid) {
                $fullRatings[$uid][$wid] = $ratings[$uid][$wid] ?? null;
            }
        }

        // 1) compute user averages
        $averages = $service->computeUserAverages($fullRatings);

        // 2) normalize (centered ratings)
        $normalized = $service->normalizeRatings($fullRatings, $averages);

        // 3) compute centered cosine similarity
        $simMatrix = $service->computeCenteredCosineSimilarity($normalized);

        // 4) sort and take top-N neighbors
        $k = 3; // configurable neighbor count
        $neighbors = $service->getTopNeighbors($simMatrix, $k, true);

        // 5) compute predictions for items not rated by target user using weighted average
        $predictions = [];
        $userRated = array_filter($fullRatings[$userId] ?? [], function($v) { return $v !== null; });
        foreach ($allWisataIds as $wid) {
            if (isset($fullRatings[$userId][$wid]) && $fullRatings[$userId][$wid] !== null) continue;
            $num = 0.0; $den = 0.0;
            foreach ($neighbors[$userId] ?? [] as $nb) {
                $v = $nb['user'];
                $sim = $nb['sim'];
                $nv = $normalized[$v][$wid] ?? null;
                if ($nv === null) continue;
                $num += $sim * $nv;
                $den += abs($sim);
            }
            $predNorm = ($den > 0) ? ($num / $den) : 0.0;
            $pred = ($averages[$userId] ?? 0.0) + $predNorm;
            $predictions[$wid] = $pred;
        }

        // 6) sort predictions descending
        arsort($predictions);

        // Build prediction details (contributions from neighbors)
        $predDetails = [];
        foreach ($predictions as $wid => $score) {
            $num = 0.0; $den = 0.0; $contribs = [];
            foreach ($neighbors[$userId] ?? [] as $nb) {
                $v = $nb['user'];
                $sim = $nb['sim'];
                $nv = $normalized[$v][$wid] ?? null;
                if ($nv === null) continue;
                $c = $sim * $nv;
                $contribs[] = ['user' => $v, 'sim' => $sim, 'nv' => $nv, 'contrib' => $c];
                $num += $c;
                $den += abs($sim);
            }
            $predNorm = ($den > 0) ? ($num / $den) : 0.0;
            $predDetails[$wid] = ['contribs' => $contribs, 'num' => $num, 'den' => $den, 'predNorm' => $predNorm, 'pred' => $score];
        }

        // Prepare rekomendasi insertion based on predicted score threshold (e.g., >=4)
        $rekomendasi = [];
        $rekomendasiToInsert = [];
        foreach ($predictions as $wid => $score) {
            if ($score >= 4.0) {
                $rekomendasi[] = $wisatas[$wid] ?? '-';
                $rekomendasiToInsert[] = ['id_user' => $userId, 'id_wisata' => $wid];
            }
        }
        if (!empty($rekomendasiToInsert)) {
            \App\Models\Rekomendasi::upsert($rekomendasiToInsert, ['id_user', 'id_wisata']);
        }

        // Ambil list wisata untuk tabel
        $wisataList = [];
        foreach ($wisatas as $wid => $nama) {
            $wisataList[] = ['id' => $wid, 'nama' => $nama];
        }

        // Buat pivot matriks rating user-wisata (fill 0 for display)
        $pivot = [];
        foreach ($users as $uid => $userEmail) {
            foreach ($wisataList as $w) {
                $pivot[$uid][$w['id']] = $ratings[$uid][$w['id']] ?? 0;
            }
        }

        // Kirim ke view rekomendasi
        // Build similarity summary + details for display (user vs others)
        $similarities = [];
        $similarityDetails = [];
        $topUser = null; $topSimilarity = 0.0;
        foreach (array_keys($users) as $otherId) {
            if ($otherId == $userId) continue;
            $sim = $simMatrix[$userId][$otherId] ?? 0.0;
            $similarities[$otherId] = $sim;
            // build detail using centered (normalized) vectors for co-rated items
            $dot = 0.0; $normA = 0.0; $normB = 0.0;
            $aVec = [];
            $bVec = [];
            $dotDetail = [];
            $normADetail = [];
            $normBDetail = [];
            foreach ($allWisataIds as $wid) {
                $a = $normalized[$userId][$wid] ?? null;
                $b = $normalized[$otherId][$wid] ?? null;
                $aVal = ($a === null) ? 0 : $a;
                $bVal = ($b === null) ? 0 : $b;
                $aVec[] = $aVal;
                $bVec[] = $bVal;
                $dot += $aVal * $bVal;
                $dotDetail[] = $aVal . '×' . $bVal . '=' . ($aVal*$bVal);
                $normA += $aVal * $aVal;
                $normADetail[] = $aVal . '²=' . ($aVal*$aVal);
                $normB += $bVal * $bVal;
                $normBDetail[] = $bVal . '²=' . ($bVal*$bVal);
            }
            $similarityDetails[$otherId] = [
                'a' => $aVec,
                'b' => $bVec,
                'dotDetail' => $dotDetail,
                'normADetail' => $normADetail,
                'normBDetail' => $normBDetail,
                'dot' => $dot,
                'normA' => $normA,
                'normB' => $normB,
                'similarity' => $sim,
            ];
            if ($sim > $topSimilarity) {
                $topSimilarity = $sim;
                $topUser = $otherId;
            }
        }
        $topUserEmail = $topUser ? ($users[$topUser] ?? '-') : '-';

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
            'wisataList' => $wisataList,
            'pivot' => $pivot,
            'predictions' => $predictions,
            'predDetails' => $predDetails,
            'averages' => $averages,
            'neighbors' => $neighbors,
            'normalized' => $normalized,
            'allWisataIds' => $allWisataIds,
            'k' => $k,
            'wisatas' => $wisatas,
        ]);
    }
}
