@extends('layouts.app')

@section('title', 'Rekomendasi')

@section('content')
<div class="container mt-4">
    <h1 class="mb-3">Rekomendasi Wisata</h1>
    <p class="lead">Dapatkan rekomendasi wisata berdasarkan kemiripan rating Anda dengan pengguna lain menggunakan metode Collaborative Filtering.</p>
    <a href="{{ route('wisatawan.rekomendasi.proses') }}" class="btn btn-success mb-4" id="btn-rekomendasi">Dapatkan Rekomendasi</a>
    <div id="loading-rekomendasi" class="text-center mb-3" style="display:none">
        <div class="spinner-border text-success" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <div>Menjalankan Algoritma Collaborative Filtering (User-Based), mohon tunggu...</div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <b>Langkah-langkah Proses Rekomendasi</b>
        </div>
        <div class="card-body">
            <ol class="mb-0">
                <li><b>Ambil data rating seluruh user</b></li>
                <li><b>Hitung kemiripan (Cosine Similarity) antara user login dan user lain</b></li>
                <li><b>Tampilkan daftar similarity seluruh user</b></li>
                <li><b>Ambil user paling mirip dan tampilkan rekomendasi wisata yang belum Anda nilai</b></li>
            </ol>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="alert alert-info mb-2">
                <b>User login:</b> {{ $email ?? '-' }}<br>
                <b>User paling mirip:</b> {{ $topUserEmail ?? '-' }}<br>
                <b>Nilai Cosine Similarity:</b> {{ number_format($topSimilarity ?? 0, 4) }}
            </div>
        </div>
    </div>

    @if(isset($similarities))
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <b>Daftar Kemiripan (Cosine Similarity) Semua User</b>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-sm mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width:40%">User</th>
                        <th style="width:20%">Similarity</th>
                        <th>Detail Perhitungan</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($similarities as $uid => $sim)
                    <tr>
                        <td>{{ $users[$uid] ?? '-' }}</td>
                        <td>{{ number_format($sim, 4) }}</td>
                        <td>
                            <button class="btn btn-link btn-sm" type="button" data-toggle="collapse" data-target="#detail-{{ $uid }}" aria-expanded="false" aria-controls="detail-{{ $uid }}">
                                Lihat Detail
                            </button>
                            <div class="collapse" id="detail-{{ $uid }}">
                                <ul class="mb-0">
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
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if(isset($rekomendasi) && count($rekomendasi) > 0)
        <div class="alert alert-success">
            <h4 class="alert-heading">Hasil Rekomendasi untuk {{ $email ?? 'Anda' }}:</h4>
            <ul class="mb-0">
                @foreach($rekomendasi as $wisata)
                    <li>{{ $wisata }}</li>
                @endforeach
            </ul>
        </div>
    @elseif(isset($rekomendasi))
        <div class="alert alert-warning">
            <h4 class="alert-heading">Tidak ada rekomendasi baru untuk {{ $email ?? 'Anda' }}.</h4>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('btn-rekomendasi').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('loading-rekomendasi').style.display = 'block';
        var url = this.getAttribute('href');
        setTimeout(function() {
            window.location.href = url;
        }, 2000);
    });
</script>
@endpush
@endsection
