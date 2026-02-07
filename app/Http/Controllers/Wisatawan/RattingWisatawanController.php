<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Ratting;
use App\Models\Wisata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RattingWisatawanController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (session('user_role') !== 'wisatawan') {
                return redirect()->route('login');
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $userId = (int) (session('user.id') ?? session('user_id', 0));

        $rattingQuery = Ratting::with('wisata')
            ->where('user_id', $userId);

        if ($query !== '') {
            $rattingQuery->where(function ($builder) use ($query) {
                $builder->where('ulasan', 'like', "%{$query}%")
                    ->orWhere('ratting', 'like', "%{$query}%")
                    ->orWhereHas('wisata', function ($wisataQuery) use ($query) {
                        $wisataQuery->where('nama', 'like', "%{$query}%")
                            ->orWhere('slug', 'like', "%{$query}%");
                    });
            });
        }

        $rattings = $rattingQuery
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('wisatawan.rattings_wisatawan.index', compact('rattings', 'query'));
    }

    public function create(): View
    {
        $wisata = Wisata::orderBy('nama')->get();

        return view('wisatawan.rattings_wisatawan.create', compact('wisata'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'wisata_id' => ['required', 'integer', 'min:1', 'exists:wisata,id'],
            'ratting' => ['required', 'integer', 'min:1', 'max:5'],
            'ulasan' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = (int) (session('user.id') ?? session('user_id', 0));

        Ratting::create($validated);

        return redirect()
            ->route('rattings-wisatawan.index')
            ->with('success', 'Ratting berhasil ditambahkan.');
    }

    public function show(Ratting $rattings_wisatawan): View
    {
        $this->ensureOwnership($rattings_wisatawan);
        $rattings_wisatawan->load('wisata');

        return view('wisatawan.rattings_wisatawan.show', [
            'ratting' => $rattings_wisatawan,
        ]);
    }

    public function edit(Ratting $rattings_wisatawan): View
    {
        $this->ensureOwnership($rattings_wisatawan);
        $wisata = Wisata::orderBy('nama')->get();

        return view('wisatawan.rattings_wisatawan.edit', [
            'ratting' => $rattings_wisatawan,
            'wisata' => $wisata,
        ]);
    }

    public function update(Request $request, Ratting $rattings_wisatawan): RedirectResponse
    {
        $this->ensureOwnership($rattings_wisatawan);

        $validated = $request->validate([
            'wisata_id' => ['required', 'integer', 'min:1', 'exists:wisata,id'],
            'ratting' => ['required', 'integer', 'min:1', 'max:5'],
            'ulasan' => ['nullable', 'string'],
        ]);

        $rattings_wisatawan->update($validated);

        return redirect()
            ->route('rattings-wisatawan.index')
            ->with('success', 'Ratting berhasil diperbarui.');
    }

    public function destroy(Ratting $rattings_wisatawan): RedirectResponse
    {
        $this->ensureOwnership($rattings_wisatawan);
        $rattings_wisatawan->delete();

        return redirect()
            ->route('rattings-wisatawan.index')
            ->with('success', 'Ratting berhasil dihapus.');
    }

    private function ensureOwnership(Ratting $ratting): void
    {
        $sessionUserId = (int) (session('user.id') ?? session('user_id', 0));
        if ((int) $ratting->user_id !== $sessionUserId) {
            abort(403, 'Akses ditolak.');
        }
    }
}
