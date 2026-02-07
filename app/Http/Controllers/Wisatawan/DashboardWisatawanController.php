<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\DashboardWisatawan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardWisatawanController extends Controller
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
        $userId = (int) (session('user.id') ?? session('user_id', 0));
        $query = trim((string) $request->query('q', ''));
        $summary = DashboardWisatawan::summaryForUser($userId, $query);

        return view('wisatawan.dashboard.index', $summary);
    }
}
