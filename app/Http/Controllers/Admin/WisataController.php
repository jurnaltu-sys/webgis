<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FotoWisata;
use App\Models\Kategori;
use App\Models\Wisata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WisataController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (session('user_role') !== 'admin') {
                return redirect()->route('login');
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        $wisataQuery = Wisata::query();

        if ($query !== '') {
            $wisataQuery->where('nama', 'like', "%{$query}%");
        }

        $wisata = $wisataQuery
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.wisata.index', compact('wisata', 'query'));
    }

    public function create(): View
    {
        $kategori = Kategori::orderBy('nama')->get();

        return view('admin.wisata.create', compact('kategori'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:160'],
            'kategori_id' => ['required', 'integer', 'min:1'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'deskripsi' => ['nullable', 'string'],
            'fasilitas' => ['nullable', 'array'],
            'jam_buka' => ['nullable', 'string', 'max:50'],
            'rating_avg' => ['nullable', 'numeric', 'min:0'],
            'jml_rating' => ['nullable', 'integer', 'min:0'],
            'foto_wisata' => ['nullable', 'array'],
            'foto_wisata.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'cover_index' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['fasilitas'] = $request->input('fasilitas', []);
        // Set default value jika null agar tidak error pada kolom NOT NULL
        $validated['slug'] = $validated['slug'] ?? '';
        $validated['deskripsi'] = $validated['deskripsi'] ?? '';
        $validated['jam_buka'] = $validated['jam_buka'] ?? '';
        $validated['rating_avg'] = $validated['rating_avg'] ?? 0;
        $validated['jml_rating'] = $validated['jml_rating'] ?? 0;
        if (empty($validated['fasilitas'])) {
            $validated['fasilitas'] = [];
        }

        DB::transaction(function () use ($validated, $request): void {
            $wisata = Wisata::create($validated);

            $files = $request->file('foto_wisata', []);
            if (!empty($files)) {
                $coverIndex = (int) $request->input('cover_index', 0);
                $totalFiles = count($files);
                if ($coverIndex < 0 || $coverIndex >= $totalFiles) {
                    $coverIndex = 0;
                }

                foreach ($files as $index => $file) {
                    $path = $file->store('wisata', 'public');

                    $wisata->foto()->create([
                        'url' => $path,
                        'is_cover' => $index === $coverIndex ? 1 : 0,
                    ]);
                }
            }
        });

        return redirect()
            ->route('wisata.index')
            ->with('success', 'Data wisata berhasil ditambahkan.');
    }

    public function show(Wisata $wisata): View
    {
        $kategori = Kategori::orderBy('nama')->get();

        return view('admin.wisata.show', compact('wisata', 'kategori'));
    }

    public function edit(Wisata $wisata): View
    {
        $kategori = Kategori::orderBy('nama')->get();

        return view('admin.wisata.edit', compact('wisata', 'kategori'));
    }

    public function update(Request $request, Wisata $wisata): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:160'],
            'kategori_id' => ['required', 'integer', 'min:1'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'deskripsi' => ['nullable', 'string'],
            'fasilitas' => ['nullable', 'array'],
            'jam_buka' => ['nullable', 'string', 'max:50'],
            'rating_avg' => ['nullable', 'numeric', 'min:0'],
            'jml_rating' => ['nullable', 'integer', 'min:0'],
            'foto_wisata' => ['nullable', 'array'],
            'foto_wisata.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $validated['fasilitas'] = $request->input('fasilitas', []);
        // Set default value jika null agar tidak error pada kolom NOT NULL
        $validated['slug'] = $validated['slug'] ?? '';
        $validated['deskripsi'] = $validated['deskripsi'] ?? '';
        $validated['jam_buka'] = $validated['jam_buka'] ?? '';
        $validated['rating_avg'] = $validated['rating_avg'] ?? 0;
        $validated['jml_rating'] = $validated['jml_rating'] ?? 0;
        if (empty($validated['fasilitas'])) {
            $validated['fasilitas'] = [];
        }

        DB::transaction(function () use ($validated, $request, $wisata): void {
            $wisata->update($validated);

            $files = $request->file('foto_wisata', []);
            if (!empty($files)) {
                $hasCover = $wisata->foto()->where('is_cover', 1)->exists();
                foreach ($files as $file) {
                    $path = $file->store('wisata', 'public');

                    $wisata->foto()->create([
                        'url' => $path,
                        'is_cover' => $hasCover ? 0 : 1,
                    ]);

                    $hasCover = true;
                }
            }
        });

        return redirect()
            ->route('wisata.index')
            ->with('success', 'Data wisata berhasil diperbarui.');
    }

    public function destroy(Wisata $wisata): RedirectResponse
    {

        DB::transaction(function () use ($wisata) {
            // Delete related FotoWisata
            foreach ($wisata->foto as $foto) {
                if ($foto->url) {
                    Storage::disk('public')->delete($foto->url);
                }
                $foto->delete();
            }

            // Delete related Ratting
            if (method_exists($wisata, 'rattings')) {
                $wisata->rattings()->delete();
            } else {
                // Fallback: delete from Ratting model if relation not defined
                \App\Models\Ratting::where('wisata_id', $wisata->id)->delete();
            }

            $wisata->delete();
        });

        return redirect()
            ->route('wisata.index')
            ->with('success', 'Data wisata berhasil dihapus.');
    }

    public function destroyFoto(Wisata $wisata, FotoWisata $foto): RedirectResponse
    {
        if ((int) $foto->wisata_id !== (int) $wisata->id) {
            abort(404);
        }

        DB::transaction(function () use ($wisata, $foto): void {
            $wasCover = (int) $foto->is_cover === 1;

            if ($foto->url) {
                Storage::disk('public')->delete($foto->url);
            }

            $foto->delete();

            if ($wasCover) {
                $newCover = $wisata->foto()->orderBy('id')->first();
                if ($newCover) {
                    $newCover->update(['is_cover' => 1]);
                }
            }
        });

        return back()->with('success', 'Gambar berhasil dihapus.');
    }

    public function setCover(Wisata $wisata, FotoWisata $foto): RedirectResponse
    {
        if ((int) $foto->wisata_id !== (int) $wisata->id) {
            abort(404);
        }

        DB::transaction(function () use ($wisata, $foto): void {
            $wisata->foto()->update(['is_cover' => 0]);
            $foto->update(['is_cover' => 1]);
        });

        return back()->with('success', 'Gambar cover berhasil diperbarui.');
    }

    // normalizeFasilitas dihapus karena tidak diperlukan lagi
}
