@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Daftar Wisata</h1>
    <div class="row">
        @foreach($wisata as $item)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <img src="{{ $item->fotoUtama() }}" class="card-img-top" alt="Foto Wisata">
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->nama }}</h5>
                        <p class="card-text">{{ $item->deskripsi ? Str::limit($item->deskripsi, 100) : '-' }}</p>
                        <a href="{{ route('wisatawan-wisata.show', $item->id) }}" class="btn btn-info">Detail</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @if($wisata->isEmpty())
        <div class="alert alert-info">Tidak ada data wisata.</div>
    @endif
</div>
@endsection
