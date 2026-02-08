<?php

namespace App\Imports;

use App\Models\Wisata;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class WisataImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        $nama = isset($row['nama']) ? trim((string) $row['nama']) : null;

        $kategoriId = isset($row['jenis']) ? (int) $row['jenis'] : (int) ($row['kategori_id'] ?? 0);

        $deskripsi = $row['deskripsi'] ?? $row['deksripsi'] ?? '';

        $fasilitasRaw = $row['fasilitas'] ?? '';
        $fasilitas = [];
        if (is_string($fasilitasRaw) && trim($fasilitasRaw) !== '') {
            $parts = array_map('trim', explode(',', $fasilitasRaw));
            $fasilitas = array_values(array_filter($parts, fn($v) => $v !== ''));
        }

        $rating = isset($row['rating']) ? (float) $row['rating'] : 0.0;
        $jmlRating = $rating > 0 ? 1 : 0;

        return new Wisata([
            'nama' => $nama,
            'slug' => $nama ? Str::slug($nama) : '',
            'kategori_id' => $kategoriId ?: 0,
            'latitude' => isset($row['latitude']) ? (float) $row['latitude'] : 0,
            'longitude' => isset($row['longitude']) ? (float) $row['longitude'] : 0,
            'deskripsi' => $deskripsi ?? '',
            'fasilitas' => $fasilitas,
            'jam_buka' => null,
            'rating_avg' => $rating,
            'jml_rating' => $jmlRating,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:150',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
