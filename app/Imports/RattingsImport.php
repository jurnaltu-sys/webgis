<?php

namespace App\Imports;

use App\Models\Ratting;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class RattingsImport implements ToCollection, WithChunkReading
{
    /**
     * Collected simple messages for user-facing feedback.
     * @var array
     */
    public array $errors = [];

    /**
     * Count of created records in this import instance.
     * @var int
     */
    protected int $createdCount = 0;
    /**
     * Process the imported rows. First row is expected to be header row.
     * Column with header 'user' must contain the user email. Other headers
     * are treated as `wisata.nama` values.
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            return;
        }

        $created = 0;
        $processedRows = 0;

        // First row is header
        $headerRow = $rows->first()->toArray();

        // Map column index => header name (trimmed)
        $headerMap = [];
        foreach ($headerRow as $colIndex => $header) {
            $headerMap[$colIndex] = trim((string) $header);
        }

        // Find user column (case-insensitive match for 'user')
        $lowered = array_map('strtolower', $headerMap);
        $userColIndex = array_search('user', $lowered, true);
        if ($userColIndex === false) {
            // No user column found; nothing to do
            return;
        }

        // Process data rows (skip header)
        $dataRows = $rows->slice(1)->values();
        foreach ($dataRows as $index => $row) {
            $processedRows++;
            $excelRowNumber = $index + 2; // header is row 1
            $cells = $row->toArray();

            $userEmail = isset($cells[$userColIndex]) ? trim((string) $cells[$userColIndex]) : '';
            if ($userEmail === '') {
                // skip rows without user email
                $msg = "Baris {$excelRowNumber}: kolom 'user' kosong, dilewati.";
                $this->errors[] = $msg;
                Log::info('RattingsImport: skipping row because user email empty', ['row' => $processedRows, 'cells' => $cells]);
                continue;
            }

            $user = User::where('email', $userEmail)->first();
            if (!$user) {
                // user not found, skip
                $msg = "Baris {$excelRowNumber}: user dengan email {$userEmail} tidak ditemukan.";
                $this->errors[] = $msg;
                Log::info('RattingsImport: user not found', ['email' => $userEmail, 'row' => $processedRows]);
                continue;
            }

            $userId = $user->id;

            // For each other column, the header is the wisata name
            foreach ($headerMap as $colIndex => $headerName) {
                if ($colIndex == $userColIndex) {
                    continue;
                }

                $headerName = trim((string) $headerName);
                if ($headerName === '') {
                    continue;
                }

                $cellValue = isset($cells[$colIndex]) ? $cells[$colIndex] : '';
                $cellValue = trim((string) $cellValue);

                // empty cell -> 0
                $ratingValue = ($cellValue === '') ? 0 : (int) $cellValue;

                // find wisata by exact nama match
                $wisata = Wisata::where('nama', $headerName)->first();
                if (!$wisata) {
                    // wisata not found, skip
                    $msg = "Baris {$excelRowNumber}: wisata '{$headerName}' tidak ditemukan.";
                    $this->errors[] = $msg;
                    Log::info('RattingsImport: wisata not found', ['wisata_nama' => $headerName, 'row' => $processedRows]);
                    continue;
                }

                // create ratting record
                Ratting::create([
                    'user_id' => $userId,
                    'wisata_id' => $wisata->id,
                    'ratting' => $ratingValue,
                    'ulasan' => null,
                ]);
                $created++;
            }
        }
        $this->createdCount += $created;

        Log::info('RattingsImport: finished chunk', ['processed_rows' => $processedRows, 'created' => $created]);
    }

    /**
     * Get collected user-friendly error messages.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Return how many records were created by this import instance.
     */
    public function getCreatedCount(): int
    {
        return $this->createdCount;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
