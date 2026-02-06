@extends('layouts.app')

@section('content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}">Kategori</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail</li>
        </ol>
    </nav>

    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            Detail Kategori
        </div>
        <div class="card-body">
            <div class="form-group">
                <label>ID</label>
                <input type="text" class="form-control" value="{{ $kategori->id }}" readonly>
            </div>
            <div class="form-group">
                <label>Nama</label>
                <input type="text" class="form-control" value="{{ $kategori->nama }}" readonly>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('kategori.edit', $kategori) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
@endsection
