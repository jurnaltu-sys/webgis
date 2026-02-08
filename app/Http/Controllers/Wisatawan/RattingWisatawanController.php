<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Ratting;
use App\Models\Wisata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

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

        $wisata = Wisata::with('foto')->orderBy('nama')->get();
        $ratedWisataIds = Ratting::where('user_id', $userId)
            ->pluck('wisata_id')
            ->unique()
            ->values()
            ->all();

        $userRattingCount = Ratting::where('user_id', $userId)->count();

        return view('wisatawan.rattings_wisatawan.index', compact('rattings', 'query', 'wisata', 'ratedWisataIds', 'userRattingCount'));
    }

    public function create(): View
    {
        $wisata = Wisata::orderBy('nama')->get();

        return view('wisatawan.rattings_wisatawan.create', compact('wisata'));
    }

    public function store(Request $request): Response
    {
        $userId = (int) (session('user.id') ?? session('user_id', 0));
        $validated = $request->validate([
            'wisata_id' => [
                'required',
                'integer',
                'min:1',
                'exists:wisata,id',
                // pastikan kombinasi user_id dan wisata_id unik
                function ($attribute, $value, $fail) use ($userId) {
                    if (Ratting::where('user_id', $userId)->where('wisata_id', $value)->exists()) {
                        $fail('Anda sudah memberikan ratting untuk wisata ini.');
                    }
                },
            ],
            'ratting' => ['required', 'integer', 'min:1', 'max:5'],
            'ulasan' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = $userId;

        $ratting = Ratting::create($validated);

        if ($request->expectsJson()) {
            $ratting->load('wisata');

            return response()->json([
                'message' => 'Ratting berhasil ditambahkan.',
                'wisata_id' => (int) $validated['wisata_id'],
                'ratting' => [
                    'id' => (int) $ratting->id,
                    'wisata_nama' => $ratting->wisata?->nama,
                    'ratting' => (int) $ratting->ratting,
                    'ulasan' => $ratting->ulasan,
                    'show_url' => route('rattings-wisatawan.show', $ratting),
                    'edit_url' => route('rattings-wisatawan.edit', $ratting),
                    'destroy_url' => route('rattings-wisatawan.destroy', $ratting),
                ],
            ], 201);
        }

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


        $userId = (int) (session('user.id') ?? session('user_id', 0));
        $validated = $request->validate([
            'wisata_id' => [
                'required',
                'integer',
                'min:1',
                'exists:wisata,id',
                // pastikan kombinasi user_id dan wisata_id unik kecuali untuk record ini sendiri
                function ($attribute, $value, $fail) use ($userId, $rattings_wisatawan) {
                    if (Ratting::where('user_id', $userId)
                        ->where('wisata_id', $value)
                        ->where('id', '!=', $rattings_wisatawan->id)
                        ->exists()) {
                        $fail('Anda sudah memberikan ratting untuk wisata ini.');
                    }
                },
            ],
            'ratting' => ['required', 'integer', 'min:1', 'max:5'],
            'ulasan' => ['nullable', 'string'],
        ]);

        $rattings_wisatawan->update($validated);

        return redirect()
            ->route('rattings-wisatawan.index')
            ->with('success', 'Ratting berhasil diperbarui.');
    }

    public function destroy(Request $request, Ratting $rattings_wisatawan): Response
    {
        $this->ensureOwnership($rattings_wisatawan);
        $rattings_wisatawan->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Ratting berhasil dihapus.',
            ]);
        }

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
