@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Modal Preview Foto -->
    <div class="modal fade" id="photoPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <img src="" alt="Foto" class="img-fluid w-100" id="photoPreviewImage">
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb bg-white px-2 py-2 small shadow-sm">
            <li class="breadcrumb-item"><a href="{{ route('dashboard-wisatawan.index') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('wisatawan-wisata.index') }}">Daftar Wisata</a></li>
            <li class="breadcrumb-item active text-success" aria-current="page">Detail Wisata</li>
        </ol>
    </nav>
    <div class="mb-4">
        <h1 class="h4 mb-1 text-success"><i class="fas fa-map-marker-alt mr-2"></i>Detail Wisata</h1>
        <p class="text-muted mb-0">Informasi lengkap tentang destinasi pariwisata.</p>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success shadow-sm mb-4 bg-light">
                @php
                    $coverFoto = $wisata->fotoUtama();
                @endphp
                @if ($coverFoto)
                    <img src="{{ $coverFoto }}" class="card-img-top" alt="Foto {{ $wisata->nama }}" style="height:220px;object-fit:cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:220px;">
                        <span class="text-muted small">Tidak ada foto</span>
                    </div>
                @endif
                <div class="card-body py-3">
                        <h5 class="card-title text-primary mb-2"><i class="fas fa-globe-asia mr-1"></i>{{ $wisata->nama }}</h5>
                        <div class="mb-2 text-muted small">
                            {{ $wisata->deskripsi ?? '-' }}
                        </div>
                        <!-- Gallery Foto Wisata -->
                        <div class="mb-3">
                            <h6 class="mb-2 text-success"><i class="fas fa-images mr-1"></i>Galeri Foto</h6>
                            <div class="photo-scroll">
                                @forelse ($wisata->foto as $foto)
                                    <img src="{{ asset('storage/' . $foto->url) }}" alt="Foto {{ $wisata->nama }}" class="gallery-img" />
                                @empty
                                    <span class="text-muted small">Tidak ada foto tambahan.</span>
                                @endforelse
                            </div>
                        </div>
                            <!-- Map Lokasi Wisata -->
                            <div class="mb-3">
                                <h6 class="mb-2 text-success"><i class="fas fa-map-marked-alt mr-1"></i>Lokasi di Peta</h6>
                                <div id="wisata-map" style="height: 260px; width: 100%; border-radius: 8px; overflow: hidden;"></div>
                            </div>
                                <!-- Fasilitas Wisata -->
                                <div class="mb-3">
                                    <h6 class="mb-2 text-success"><i class="fas fa-concierge-bell mr-1"></i>Fasilitas</h6>
                                    @php
                                        $fasilitas = [];
                                        if (!empty($wisata->fasilitas)) {
                                            $fasilitas = is_array($wisata->fasilitas) ? $wisata->fasilitas : json_decode($wisata->fasilitas, true);
                                        }
                                    @endphp
                                    @if (!empty($fasilitas) && is_array($fasilitas))
                                        <ul class="list-inline mb-0">
                                            @foreach ($fasilitas as $item)
                                                <li class="list-inline-item mb-2">
                                                    <span class="badge badge-pill badge-info px-3 py-2"><i class="fas fa-check mr-1"></i>{{ $item }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted small">Tidak ada data fasilitas.</span>
                                    @endif
                                </div>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item"><strong><i class="fas fa-tags mr-1"></i>Kategori:</strong> {{ $wisata->kategori->nama ?? '-' }}</li>
                            <li class="list-group-item"><strong><i class="fas fa-map-marker-alt mr-1"></i>Koordinat:</strong> {{ $wisata->latitude }}, {{ $wisata->longitude }}</li>
                        </ul>
                        <a href="{{ route('wisatawan-wisata.index') }}" class="btn btn-sm btn-outline-success"><i class="fas fa-arrow-left mr-1"></i>Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        .card-img-top {
            height: 220px;
            object-fit: cover;
        }
        .photo-scroll {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px;
            width: 100%;
        }
        .gallery-img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            border: 2px solid #eee;
        }
        #wisata-map {
            height: 260px;
            width: 100%;
            border-radius: 8px;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.gallery-img', function () {
                var url = $(this).attr('src');
                var previewImage = document.getElementById('photoPreviewImage');
                if (previewImage && url) {
                    previewImage.src = url;
                    $('#photoPreviewModal').modal('show');
                }
            });
            // Inisialisasi Leaflet Map
            var lat = {{ $wisata->latitude ?? 'null' }};
            var lng = {{ $wisata->longitude ?? 'null' }};
            if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                    var map = L.map('wisata-map').setView([lat, lng], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(map);
                    var redIcon = new L.Icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });
                    var marker = L.marker([lat, lng], { icon: redIcon }).addTo(map)
                        .bindPopup('<strong>{{ $wisata->nama }}<\/strong><br>{{ $wisata->alamat ?? '-' }}');
            } else {
                $('#wisata-map').html('<div class="text-muted small p-3">Koordinat lokasi tidak tersedia.</div>');
            }
        });
    </script>
@endpush
@endsection
