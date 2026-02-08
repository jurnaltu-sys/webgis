<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    /**
     * Map a row from the spreadsheet to a User model.
     */
    public function model(array $row)
    {
        $password = (isset($row['password']) && $row['password'] !== '')
            ? Hash::make($row['password'])
            : Hash::make('password');

        $role = isset($row['role']) && in_array($row['role'], ['wisatawan', 'admin'])
            ? $row['role']
            : 'wisatawan';

        return new User([
            'name' => $row['name'] ?? null,
            'email' => $row['email'] ?? null,
            'password' => $password,
            'role' => $role,
        ]);
    }

    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
