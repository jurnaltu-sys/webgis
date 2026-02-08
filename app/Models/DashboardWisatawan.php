<?php

namespace App\Models;

class DashboardWisatawan
{
    public static function summaryForUser(int $userId, string $query = ''): array
    {
        $rattingQuery = Ratting::query()->where('user_id', $userId);

        $totalWisata = Wisata::query()->count();
        $totalKategori = Kategori::query()->count();
        $totalRattingSaya = (clone $rattingQuery)->count();
        $avgRattingSaya = (clone $rattingQuery)->avg('ratting');
        $latestRattings = (clone $rattingQuery)
            ->with('wisata')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $searchResults = Wisata::query()
            ->with('foto')
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where('nama', 'like', "%{$query}%");
            })
            ->orderBy('nama')
            ->take(20)
            ->get();

        return [
            'totalWisata' => $totalWisata,
            'totalKategori' => $totalKategori,
            'totalRattingSaya' => $totalRattingSaya,
            'avgRattingSaya' => $avgRattingSaya,
            'latestRattings' => $latestRattings,
            'searchResults' => $searchResults,
            'searchQuery' => $query,
        ];
    }
}
