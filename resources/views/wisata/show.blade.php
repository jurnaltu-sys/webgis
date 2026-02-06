@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Detail Wisata</h1>

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $wisata->id }}</dd>

                <dt class="col-sm-3">Nama</dt>
                <dd class="col-sm-9">{{ $wisata->nama }}</dd>

                <dt class="col-sm-3">Slug</dt>
                <dd class="col-sm-9">{{ $wisata->slug }}</dd>

                <dt class="col-sm-3">Kategori ID</dt>
                <dd class="col-sm-9">{{ $wisata->kategori_id }}</dd>

                <dt class="col-sm-3">Latitude</dt>
                <dd class="col-sm-9">{{ $wisata->latitude }}</dd>

                <dt class="col-sm-3">Longitude</dt>
                <dd class="col-sm-9">{{ $wisata->longitude }}</dd>

                <dt class="col-sm-3">Deskripsi</dt>
                <dd class="col-sm-9">{{ $wisata->deskripsi }}</dd>

                <dt class="col-sm-3">Fasilitas</dt>
                <dd class="col-sm-9">
                    <pre class="mb-0">{{ json_encode($wisata->fasilitas ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                </dd>

                <dt class="col-sm-3">Jam Buka</dt>
                <dd class="col-sm-9">{{ $wisata->jam_buka ?? '-' }}</dd>

                <dt class="col-sm-3">Rating Avg</dt>
                <dd class="col-sm-9">{{ number_format((float) $wisata->rating_avg, 2) }}</dd>

                <dt class="col-sm-3">Jumlah Rating</dt>
                <dd class="col-sm-9">{{ $wisata->jml_rating }}</dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ route('wisata.edit', $wisata) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('wisata.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
@endsection
