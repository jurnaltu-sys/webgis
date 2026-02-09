<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Wisata;
use App\Models\Ratting;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RattingDatasetExport implements FromArray, WithHeadings
{
    protected $users;
    protected $wisataList;
    protected $pivot;

    public function __construct($users, $wisataList, $pivot)
    {
        $this->users = $users;
        $this->wisataList = $wisataList;
        $this->pivot = $pivot;
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->users as $user) {
            $row = [
                $user->name . " (" . $user->email . ")"
            ];
            foreach ($this->wisataList as $wisata) {
                $row[] = $this->pivot[$user->id][$wisata->id] ?? '0';
            }
            $data[] = $row;
        }
        return $data;
    }

    public function headings(): array
    {
        $headings = ["User"];
        foreach ($this->wisataList as $wisata) {
            $headings[] = $wisata->nama;
        }
        return $headings;
    }
}
