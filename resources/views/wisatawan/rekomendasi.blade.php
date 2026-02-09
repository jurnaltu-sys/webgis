@extends('layouts.app')

@section('title', 'Rekomendasi')

@section('content')
<div class="container mt-4">
    <h1>Rekomendasi Wisata</h1>
    <p>Halaman ini akan menampilkan rekomendasi wisata untuk Anda.</p>
    <a href="{{ route('wisatawan.rekomendasi.proses') }}" class="btn btn-success">Proses</a>

    <div class="mt-4">
        <h5>Proses Perhitungan Collaborative Filtering:</h5>
        <ul>
            <li>User login: <b>{{ $email ?? '-' }}</b></li>
            <li>User paling mirip: <b>{{ $topUserEmail ?? '-' }}</b></li>
            <li>Nilai Cosine Similarity: <b>{{ number_format($topSimilarity ?? 0, 4) }}</b></li>
        </ul>
        @if(isset($similarities))
        <details>
            <summary>Daftar similarity semua user</summary>
            <ul>
                @foreach($similarities as $uid => $sim)
                    <li>
                        <b>{{ $users[$uid] ?? '-' }}</b>: {{ number_format($sim, 4) }}
                        <details>
                            <summary>Lihat proses perhitungan</summary>
                            <ul>
                                <li>Vektor rating user login: {{ json_encode($similarityDetails[$uid]['a'] ?? []) }}</li>
                                <li>Vektor rating user ini: {{ json_encode($similarityDetails[$uid]['b'] ?? []) }}</li>
                                <li>Dot Product: <br>
                                    {{ implode(' + ', $similarityDetails[$uid]['dotDetail'] ?? []) }} = <b>{{ $similarityDetails[$uid]['dot'] ?? 0 }}</b>
                                </li>
                                <li>Norm User login: <br>
                                    {{ implode(' + ', $similarityDetails[$uid]['normADetail'] ?? []) }} = <b>{{ $similarityDetails[$uid]['normA'] ?? 0 }}</b>
                                </li>
                                <li>Norm User ini: <br>
                                    {{ implode(' + ', $similarityDetails[$uid]['normBDetail'] ?? []) }} = <b>{{ $similarityDetails[$uid]['normB'] ?? 0 }}</b>
                                </li>
                                <li>Rumus:<br>
                                    <b>Cosine Similarity = Dot Product / (sqrt(Norm User login) × sqrt(Norm User ini))</b><br>
                                    = {{ $similarityDetails[$uid]['dot'] ?? 0 }} / (√{{ $similarityDetails[$uid]['normA'] ?? 0 }} × √{{ $similarityDetails[$uid]['normB'] ?? 0 }})<br>
                                    = <b>{{ number_format($similarityDetails[$uid]['similarity'] ?? 0, 4) }}</b>
                                </li>
                            </ul>
                        </details>
                    </li>
                @endforeach
            </ul>
        </details>
        @endif
    </div>

    @if(isset($rekomendasi) && count($rekomendasi) > 0)
        <div class="mt-4">
            <h4>Hasil Rekomendasi untuk {{ $email ?? 'Anda' }}:</h4>
            <ul>
                @foreach($rekomendasi as $wisata)
                    <li>{{ $wisata }}</li>
                @endforeach
            </ul>
        </div>
    @elseif(isset($rekomendasi))
        <div class="mt-4">
            <h4>Tidak ada rekomendasi baru untuk {{ $email ?? 'Anda' }}.</h4>
        </div>
    @endif
</div>
@endsection
