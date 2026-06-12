@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h4 mb-1 text-success"><i class="fas fa-globe-asia mr-2"></i>Pariwisata PANIAI</h1>
        <p class="text-muted mb-0">Jelajahi destinasi wisata terbaik di PANIAI.</p>
    </div>
    <div class="row">
        @forelse($wisata as $item)
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-success shadow-sm bg-light">
                    @php
                        $coverFoto = $item->fotoUtama();
                    @endphp
                    @if ($coverFoto)
                        <img src="{{ $coverFoto }}" class="card-img-top" alt="Foto {{ $item->nama }}" style="height:150px;object-fit:cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height:150px;">
                            <span class="text-muted small">Tidak ada foto</span>
                        </div>
                    @endif
                    <div class="card-body py-2">
                        <h6 class="card-title mb-1 text-primary"><i class="fas fa-map-marker-alt mr-1"></i>{{ $item->nama }}</h6>
                        <div class="mb-1 text-muted small">
                            Koordinat: {{ $item->latitude }}, {{ $item->longitude }}
                        </div>
                        <div class="mb-2 text-muted small">
                            {{ $item->deskripsi ? Str::limit($item->deskripsi, 60) : '-' }}
                        </div>
                        <a href="{{ route('wisatawan-wisata.show', $item->id) }}" class="btn btn-sm btn-outline-success small">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info mb-0 small">Belum ada data wisata.</div>
            </div>
        @endforelse
    </div>
</div>
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        .card-img-top {
            height: 150px;
            object-fit: cover;
        }
    </style>
@endpush
@endsection
