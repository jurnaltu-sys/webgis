@extends('layouts.app')

@section('content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('wisata.index') }}">Wisata</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail</li>
        </ol>
    </nav>

    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            Detail Wisata
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>ID</label>
                        <input type="text" class="form-control" value="{{ $wisata->id }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" class="form-control" value="{{ $wisata->nama }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" class="form-control" value="{{ $wisata->slug }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <select class="form-control" disabled>
                            @foreach ($kategori as $item)
                                <option value="{{ $item->id }}" {{ (string) $wisata->kategori_id === (string) $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Latitude</label>
                            <input type="text" id="latitude" class="form-control" value="{{ $wisata->latitude }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Longitude</label>
                            <input type="text" id="longitude" class="form-control" value="{{ $wisata->longitude }}" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Peta Lokasi</label>
                        <div id="map" class="rounded border"></div>
                    </div>

                    <div class="form-group">
                        <label>Jam Buka</label>
                        <input type="text" class="form-control" value="{{ $wisata->jam_buka ?? '-' }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea class="form-control" rows="4" readonly>{{ $wisata->deskripsi }}</textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    @if ($wisata->foto && $wisata->foto->count())
                        <div class="mb-3">
                            <label>Gambar</label>
                            <div class="row mt-2">
                                @foreach ($wisata->foto as $foto)
                                    <div class="col-6 mb-3">
                                        <div class="position-relative">
                                            <img
                                                src="{{ asset('storage/' . $foto->url) }}"
                                                alt="{{ $wisata->nama }}"
                                                class="img-fluid rounded border"
                                            >
                                            @if ((int) $foto->is_cover === 1)
                                                <span class="badge badge-primary position-absolute" style="top: 6px; left: 6px;">Cover</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label>Fasilitas</label>
                        <div class="border rounded p-3">
                            @php
                                $fasilitasOptions = ['Parkir', 'Toilet', 'Mushola', 'Restoran', 'Wifi', 'Penginapan'];
                                $selectedFasilitas = $wisata->fasilitas ?? [];
                            @endphp
                            <div class="row">
                                @foreach ($fasilitasOptions as $option)
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input" id="fasilitas_{{ $loop->index }}" {{ in_array($option, $selectedFasilitas, true) ? 'checked' : '' }} disabled>
                                            <label class="custom-control-label" for="fasilitas_{{ $loop->index }}">{{ $option }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Rating Avg</label>
                            <input type="text" class="form-control" value="{{ number_format((float) $wisata->rating_avg, 2) }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Jumlah Rating</label>
                            <input type="text" class="form-control" value="{{ $wisata->jml_rating }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <!--
            <a href="{{ route('wisata.edit', $wisata) }}" class="btn btn-warning">Edit</a>
            -->
            <a href="{{ route('wisata.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    />
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
    ></script>

    <style>
        #map {
            height: 240px;
            width: 100%;
        }
    </style>

    <script>
        (function () {
            var latInput = document.getElementById('latitude');
            var lngInput = document.getElementById('longitude');
            var defaultLat = parseFloat(latInput.value) || -6.20000000;
            var defaultLng = parseFloat(lngInput.value) || 106.81666667;

            var map = L.map('map').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.marker([defaultLat, defaultLng]).addTo(map);
        })();
    </script>
@endsection
