@extends('layouts.app')

@section('content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}">Kategori</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            Form Edit Kategori
        </div>
        <div class="card-body">
            <form action="{{ route('kategori.update', $kategori) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama', $kategori->nama) }}" class="form-control @error('nama') is-invalid @enderror" maxlength="50" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex">
                    <a href="{{ route('kategori.index') }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
