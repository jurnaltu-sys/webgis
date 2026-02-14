@extends('layouts.app')

@section('title', 'Rekomendasi Wisata')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h4 mb-1 text-success"><i class="fas fa-star mr-2"></i>Rekomendasi Wisata</h1>
        <p class="text-muted mb-0">Dapatkan rekomendasi wisata berdasarkan kemiripan rating Anda dengan pengguna lain menggunakan metode Collaborative Filtering.</p>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="alert alert-info mb-2 shadow-sm">
                <b><i class="fas fa-user mr-1"></i>{{ $email ?? 'User login' }}:</b><br>
                <b><i class="fas fa-user-friends mr-1"></i>User paling mirip:</b> {{ $topUserEmail ?? '-' }}<br>
                <b><i class="fas fa-percentage mr-1"></i>Nilai Cosine Similarity:</b> {{ number_format($topSimilarity ?? 0, 4) }}
            </div>
        </div>
        <div class="col-md-6 text-md-right text-center">
            <a href="{{ route('wisatawan.rekomendasi.proses') }}" class="btn btn-success mb-2" id="btn-rekomendasi">
                <i class="fas fa-magic mr-1"></i> Dapatkan Rekomendasi
            </a>
            <div id="loading-rekomendasi" class="text-center mb-2" style="display:none">
                <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div>Menjalankan Algoritma Collaborative Filtering (User-Based), mohon tunggu...</div>
            </div>
        </div>
    </div>

   
   
    @if(request()->routeIs('wisatawan.rekomendasi.proses'))
    {{-- Tabel dipindahkan ke bawah --}}
    @php $showDatasetTable = true; @endphp
    @endif
  

    @if(isset($similarities))
    <div class="card mb-4 border-secondary shadow-sm">
        <div class="card-header bg-secondary text-white">
            <b><i class="fas fa-users mr-1"></i>Daftar Kemiripan (Cosine Similarity) Semua User</b>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-sm mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width:40%"><i class="fas fa-user"></i> User</th>
                        <th style="width:20%"><i class="fas fa-percentage"></i> Similarity</th>
                        <th><i class="fas fa-info-circle"></i> Detail Perhitungan</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($similarities as $uid => $sim)
                    <tr>
                        <td>{{ $users[$uid] ?? '-' }}</td>
                        <td>{{ number_format($sim, 4) }}</td>
                        <td>
                            <button class="btn btn-link btn-sm text-primary" type="button" data-toggle="collapse" data-target="#detail-{{ $uid }}" aria-expanded="false" aria-controls="detail-{{ $uid }}">
                                <i class="fas fa-search mr-1"></i> Lihat Detail
                            </button>
                            <div class="collapse" id="detail-{{ $uid }}">
                                <ul class="mb-0">
                                    <li>Vektor rating {{ $email ?? 'user login' }}: {{ json_encode($similarityDetails[$uid]['a'] ?? []) }}</li>
                                    <li>Vektor rating {{ $users[$uid] ?? 'user ini' }}: {{ json_encode($similarityDetails[$uid]['b'] ?? []) }}</li>
                                    <li>Dot Product: <br>
                                        {{ implode(' + ', $similarityDetails[$uid]['dotDetail'] ?? []) }} = <b>{{ $similarityDetails[$uid]['dot'] ?? 0 }}</b>
                                    </li>
                                    <li>Norm {{ $email ?? 'User login' }}: <br>
                                        {{ implode(' + ', $similarityDetails[$uid]['normADetail'] ?? []) }} = <b>{{ $similarityDetails[$uid]['normA'] ?? 0 }}</b>
                                    </li>
                                    <li>Norm {{ $users[$uid] ?? 'user ini' }}: <br>
                                        {{ implode(' + ', $similarityDetails[$uid]['normBDetail'] ?? []) }} = <b>{{ $similarityDetails[$uid]['normB'] ?? 0 }}</b>
                                    </li>
                                    <li>Rumus:<br>
                                        <b>Cosine Similarity = Dot Product / (sqrt(Norm {{ $email ?? 'User login' }}) × sqrt(Norm {{ $users[$uid] ?? 'user ini' }}))</b><br>
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

    {{-- Tabel Dataset User Login & User Paling Mirip dipindahkan ke bawah --}}
    @if(isset($showDatasetTable) && $showDatasetTable)
    <div class="card border-success shadow-sm bg-light mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3 text-primary"><i class="fas fa-list-ol mr-1"></i>Dataset {{ $email ?? 'User Login' }} & User Paling Mirip</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="align-middle">User</th>
                            @foreach($wisataList as $wisata)
                                <th class="align-middle">{{ $wisata['nama'] ?? ($wisata->nama ?? '-') }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $uid => $userEmail)
                            @if((isset($email) && $userEmail == $email) || (isset($topUserEmail) && $userEmail == $topUserEmail))
                                <tr @if(isset($email) && $userEmail == $email) style="background-color:#fd7e14;color:white" @endif>
                                    <td class="align-middle">
                                        <span class="font-weight-bold">{{ $userEmail }}</span>
                                        @if(isset($email) && $userEmail == $email)
                                            <br><small class="text-white">({{ $email }})</small>
                                        @elseif(isset($topUserEmail) && $userEmail == $topUserEmail)
                                            <br><small class="text-muted">(User Paling Mirip)</small>
                                        @endif
                                    </td>
                                    @foreach($wisataList as $wisata)
                                        @php
                                            $isRekom = isset($rekomendasi) && in_array($wisata['nama'] ?? ($wisata->nama ?? ''), $rekomendasi);
                                        @endphp
                                        <td class="align-middle text-center" @if($isRekom) style="background-color:#28a745;color:white" @endif>
                                            {{ $pivot[$uid][$wisata['id'] ?? ($wisata->id ?? 0)] ?? '0' }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if(isset($rekomendasi) && count($rekomendasi) > 0)
        <div class="alert alert-success shadow-sm">
            <h4 class="alert-heading"><i class="fas fa-check-circle mr-1"></i>Hasil Rekomendasi untuk {{ $email ?? 'Anda' }}:</h4>
            <ul class="mb-0">
                @foreach($rekomendasi as $wisata)
                    <li><i class="fas fa-map-marker-alt text-success mr-1"></i>{{ $wisata }}</li>
                @endforeach
            </ul>
        </div>
    @elseif(isset($rekomendasi))
        <div class="alert alert-warning shadow-sm">
            <h4 class="alert-heading"><i class="fas fa-exclamation-circle mr-1"></i>Tidak ada rekomendasi baru untuk {{ $email ?? 'Anda' }}.</h4>
        </div>
    @endif
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        .card-img-top {
            height: 150px;
            object-fit: cover;
        }
        .shadow-sm {
            box-shadow: 0 2px 6px rgba(0,0,0,0.07);
        }
    </style>
@endpush

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
