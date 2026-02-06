@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Edit Wisata</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('wisata.update', $wisata) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama', $wisata->nama) }}" class="form-control @error('nama') is-invalid @enderror" maxlength="150" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $wisata->slug) }}" class="form-control @error('slug') is-invalid @enderror" maxlength="160" required>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="kategori_id">Kategori ID</label>
                    <input type="number" name="kategori_id" id="kategori_id" value="{{ old('kategori_id', $wisata->kategori_id) }}" class="form-control @error('kategori_id') is-invalid @enderror" min="1" required>
                    @error('kategori_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="latitude">Latitude</label>
                        <input type="number" step="0.00000001" name="latitude" id="latitude" value="{{ old('latitude', $wisata->latitude) }}" class="form-control @error('latitude') is-invalid @enderror" required>
                        @error('latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="longitude">Longitude</label>
                        <input type="number" step="0.00000001" name="longitude" id="longitude" value="{{ old('longitude', $wisata->longitude) }}" class="form-control @error('longitude') is-invalid @enderror" required>
                        @error('longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi', $wisata->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="fasilitas">Fasilitas (JSON)</label>
                    <textarea name="fasilitas" id="fasilitas" rows="3" class="form-control @error('fasilitas') is-invalid @enderror" required>{{ old('fasilitas', json_encode($wisata->fasilitas ?? [], JSON_UNESCAPED_UNICODE)) }}</textarea>
                    @error('fasilitas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="jam_buka">Jam Buka (opsional)</label>
                    <input type="text" name="jam_buka" id="jam_buka" value="{{ old('jam_buka', $wisata->jam_buka) }}" class="form-control @error('jam_buka') is-invalid @enderror" maxlength="50">
                    @error('jam_buka')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="rating_avg">Rating Avg</label>
                        <input type="number" step="0.01" name="rating_avg" id="rating_avg" value="{{ old('rating_avg', $wisata->rating_avg) }}" class="form-control @error('rating_avg') is-invalid @enderror" min="0">
                        @error('rating_avg')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="jml_rating">Jumlah Rating</label>
                        <input type="number" name="jml_rating" id="jml_rating" value="{{ old('jml_rating', $wisata->jml_rating) }}" class="form-control @error('jml_rating') is-invalid @enderror" min="0">
                        @error('jml_rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex">
                    <a href="{{ route('wisata.index') }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
