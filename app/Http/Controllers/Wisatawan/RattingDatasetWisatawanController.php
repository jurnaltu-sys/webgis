<?php
namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wisata;
use App\Models\Ratting;
use Illuminate\View\View;

use App\Exports\RattingDatasetExport;
use Maatwebsite\Excel\Facades\Excel;

class RattingDatasetWisatawanController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (session('user_role') !== 'wisatawan') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(): View
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
        return view('wisatawan.rattings.dataset', [
            'users' => $users,
            'wisataList' => $wisataList,
            'pivot' => $pivot,
        ]);
    }

    public function exportExcel()
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
        $export = new RattingDatasetExport($users, $wisataList, $pivot);
        return Excel::download($export, 'dataset_ratting.xlsx');
    }
}
