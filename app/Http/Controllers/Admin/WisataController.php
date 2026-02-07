<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Wisata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'slug' => ['required', 'string', 'max:160'],
            'kategori_id' => ['required', 'integer', 'min:1'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'deskripsi' => ['required', 'string'],
            'fasilitas' => ['required'],
            'jam_buka' => ['nullable', 'string', 'max:50'],
            'rating_avg' => ['nullable', 'numeric', 'min:0'],
            'jml_rating' => ['nullable', 'integer', 'min:0'],
            'foto_wisata' => ['nullable', 'array'],
            'foto_wisata.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $validated['fasilitas'] = $this->normalizeFasilitas($request->input('fasilitas'));

        DB::transaction(function () use ($validated, $request): void {
            $wisata = Wisata::create($validated);

            $files = $request->file('foto_wisata', []);
            if (!empty($files)) {
                $isFirst = true;
                foreach ($files as $file) {
                    $path = $file->store('wisata', 'public');

                    $wisata->foto()->create([
                        'url' => $path,
                        'is_cover' => $isFirst ? 1 : 0,
                    ]);

                    $isFirst = false;
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
            'slug' => ['required', 'string', 'max:160'],
            'kategori_id' => ['required', 'integer', 'min:1'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'deskripsi' => ['required', 'string'],
            'fasilitas' => ['required'],
            'jam_buka' => ['nullable', 'string', 'max:50'],
            'rating_avg' => ['nullable', 'numeric', 'min:0'],
            'jml_rating' => ['nullable', 'integer', 'min:0'],
            'foto_wisata' => ['nullable', 'array'],
            'foto_wisata.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $validated['fasilitas'] = $this->normalizeFasilitas($request->input('fasilitas'));

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
        $wisata->delete();

        return redirect()
            ->route('wisata.index')
            ->with('success', 'Data wisata berhasil dihapus.');
    }

    private function normalizeFasilitas(mixed $input): array
    {
        if (is_string($input)) {
            $decoded = json_decode($input, true);
            $input = $decoded ?? $input;
        }

        if (!is_array($input) || $input === []) {
            throw ValidationException::withMessages([
                'fasilitas' => 'Fasilitas wajib diisi dan harus berupa daftar.',
            ]);
        }

        return array_values($input);
    }
}
