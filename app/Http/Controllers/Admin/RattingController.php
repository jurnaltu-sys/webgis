<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ratting;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;




class RattingController extends Controller
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
        $rattingQuery = Ratting::with(['user', 'wisata']);
        if ($query !== '') {
            $rattingQuery->where(function ($builder) use ($query) {
                $builder->where('ulasan', 'like', "%{$query}%")
                    ->orWhere('ratting', 'like', "%{$query}%")
                    ->orWhereHas('user', function ($userQuery) use ($query) {
                        $userQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%");
                    })
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
        return view('admin.rattings.index', compact('rattings', 'query'));
    }

    public function create(): View
    {
        $users = User::where('role', 'wisatawan')
            ->orderBy('name')
            ->get();
        $wisata = Wisata::orderBy('nama')->get();
        return view('admin.rattings.create', compact('users', 'wisata'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'min:1', 'exists:users,id'],
            'wisata_id' => ['required', 'integer', 'min:1', 'exists:wisata,id'],
            'ratting' => ['required', 'integer', 'min:1', 'max:5'],
            'ulasan' => ['nullable', 'string'],
        ]);
        Ratting::create($validated);
        return redirect()
            ->route('rattings.index')
            ->with('success', 'Ratting berhasil ditambahkan.');
    }

    public function show(Ratting $ratting): View
    {
        $ratting->load(['user', 'wisata']);
        return view('admin.rattings.show', compact('ratting'));
    }

    public function edit(Ratting $ratting): View
    {
        $users = User::where('role', 'wisatawan')
            ->orderBy('name')
            ->get();
        $wisata = Wisata::orderBy('nama')->get();
        return view('admin.rattings.edit', compact('ratting', 'users', 'wisata'));
    }

    public function update(Request $request, Ratting $ratting): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'min:1', 'exists:users,id'],
            'wisata_id' => ['required', 'integer', 'min:1', 'exists:wisata,id'],
            'ratting' => ['required', 'integer', 'min:1', 'max:5'],
            'ulasan' => ['nullable', 'string'],
        ]);
        $ratting->update($validated);
        return redirect()
            ->route('rattings.index')
            ->with('success', 'Ratting berhasil diperbarui.');
    }

    public function destroy(Ratting $ratting): RedirectResponse
    {
        $ratting->delete();
        return redirect()
            ->route('rattings.index')
            ->with('success', 'Ratting berhasil dihapus.');
    }

    /**
     * Show rattings in pivot table format: user as row, wisata as column, ratting as cell.
     */
    public function excelView(): View
    {
        $userId = request('user_id');
        $usersQuery = User::where('role', 'wisatawan')->orderBy('name');
        if ($userId) {
            $usersQuery->where('id', $userId);
        }
        $users = $usersQuery->get();
        $wisataList = Wisata::orderBy('nama')->get();
        $rattings = Ratting::get();
        $pivot = [];
        foreach ($rattings as $rat) {
            $pivot[$rat->user_id][$rat->wisata_id] = $rat->ratting;
        }
        return view('admin.rattings.excelview', [
            'users' => $users,
            'wisataList' => $wisataList,
            'pivot' => $pivot,
        ]);
    }
    /**
     * Show form to edit/add all ratting for a user (pivot style).
     */
    public function editExcelUser($userId): View
    {
        $user = User::findOrFail($userId);
        $wisataList = Wisata::orderBy('nama')->get();
        // Ambil ratting existing untuk user ini
        $rattings = Ratting::where('user_id', $userId)->get()->keyBy('wisata_id');
        return view('admin.rattings.exceluser_edit', [
            'user' => $user,
            'wisataList' => $wisataList,
            'rattings' => $rattings,
        ]);
    }

    /**
     * Proses simpan seluruh ratting user (pivot style).
     */
    public function updateExcelUser(Request $request, $userId): RedirectResponse
    {
        $user = User::findOrFail($userId);
        $wisataList = Wisata::pluck('id');
        $data = $request->validate([
            'ratting' => ['array'],
            'ratting.*' => ['nullable', 'integer', 'min:0', 'max:5'],
            'ulasan' => ['array'],
            'ulasan.*' => ['nullable', 'string'],
        ]);
        foreach ($wisataList as $wisataId) {
            $nilai = $data['ratting'][$wisataId] ?? null;
            $ulasan = $data['ulasan'][$wisataId] ?? null;
            if ($nilai !== null && $nilai > 0) {
                Ratting::updateOrCreate(
                    ['user_id' => $userId, 'wisata_id' => $wisataId],
                    ['ratting' => $nilai, 'ulasan' => $ulasan]
                );
            } else {
                // Jika kosong/hapus, hapus ratting
                Ratting::where('user_id', $userId)->where('wisata_id', $wisataId)->delete();
            }
        }
        return redirect()->route('rattings.excelview')->with('success', 'Data ratting user berhasil disimpan.');
    }

    /**
     * Hapus semua ratting user (pivot style).
     */
    public function deleteExcelUser($userId): RedirectResponse
    {
        Ratting::where('user_id', $userId)->delete();
        return redirect()->route('rattings.excelview')->with('success', 'Semua data ratting user dihapus.');
    }
}
