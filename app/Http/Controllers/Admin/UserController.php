<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
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

        $userQuery = User::query();

        if ($query !== '') {
            $userQuery->where(function ($builder) use ($query) {
                $builder->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });
        }

        $users = $userQuery
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'query'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:wisatawan,admin'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:wisatawan,admin'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
