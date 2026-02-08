@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Detail Wisata</h1>
    <div class="card mb-4">
        <img src="{{ $wisata->fotoUtama() }}" class="card-img-top" alt="Foto Wisata">
        <div class="card-body">
            <h5 class="card-title">{{ $wisata->nama }}</h5>
            <p class="card-text">{{ $wisata->deskripsi ?? '-' }}</p>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Alamat:</strong> {{ $wisata->alamat ?? '-' }}</li>
                <li class="list-group-item"><strong>Kategori:</strong> {{ $wisata->kategori->nama ?? '-' }}</li>
            </ul>
        </div>
    </div>
    <a href="{{ route('wisatawan-wisata.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
