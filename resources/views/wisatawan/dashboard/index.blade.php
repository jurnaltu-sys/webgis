@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="text-muted">Pencarian</h6>
                    <form method="GET" action="{{ route('dashboard-wisatawan.index') }}">
                        <div class="form-group">
                            <label for="search" class="sr-only">Cari</label>
                            <input type="text" name="q" id="search" class="form-control" placeholder="Cari nama wisata..." value="{{ $searchQuery ?? '' }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Cari</button>
                    </form>
                    <hr>
                    <h6 class="text-muted">Hasil Pencarian</h6>
                    @if ($searchResults->isEmpty())
                        <p class="text-muted mb-0">Tidak ada hasil.</p>
                    @else
                        <div class="list-group">
                            @foreach ($searchResults as $item)
                                <label class="list-group-item d-flex align-items-center small mb-0" style="cursor:pointer;">
                                    <input type="checkbox"
                                        class="mr-2 js-wisata-checkbox"
                                        aria-label="Pilih {{ $item->nama }}"
                                        data-lat="{{ $item->latitude }}"
                                        data-lng="{{ $item->longitude }}"
                                        data-name="{{ $item->nama }}"
                                        data-photos='@json($item->foto->map(function ($foto) { return asset("storage/" . $foto->url); })->values())'
                                        checked>
                                    <strong class="mb-0">{{ $item->nama }}</strong>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h4 mb-1">Dashboard Wisatawan</h1>
                    <p class="text-muted mb-0">Selamat datang, {{ session('user_email', '-') }}</p>
                </div>
                <a href="{{ route('rattings-wisatawan.create') }}" class="btn btn-primary">Tambah Ratting</a>
            </div>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div id="wisata-map"></div>
                </div>
            </div>
            <!-- Rekomendasi untuk anda -->
            <div class="card mb-4 border-success">
                <div class="card-header py-2 bg-success text-white">
                    <h6 class="mb-0 small">Rekomendasi by CF(User-Based)</h6>
                </div>
                <div class="card-body py-3">
                    <div class="row">
                        @forelse ($rekomendasiWisata as $wisata)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-success shadow-sm">
                                    @if (!empty($wisata['foto']) && count($wisata['foto']) > 0)
                                        <img src="{{ $wisata['foto'][0] }}" class="card-img-top" alt="Foto {{ $wisata['nama'] }}" style="height:150px;object-fit:cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height:150px;">
                                            <span class="text-muted small">Tidak ada foto</span>
                                        </div>
                                    @endif
                                    <div class="card-body py-2">
                                        <h6 class="card-title mb-1 small">{{ $wisata['nama'] }}</h6>
                                        <div class="mb-1 text-muted small">
                                            Koordinat: {{ $wisata['latitude'] }}, {{ $wisata['longitude'] }}
                                        </div>
                                        <a href="{{ route('wisata.show', $wisata['id']) }}" class="btn btn-sm btn-outline-primary small">Lihat Detail</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info mb-0 small">Belum ada rekomendasi wisata untuk anda.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card mb-4 border-warning">
                <div class="card-header d-flex justify-content-between align-items-center bg-warning text-white">
                    <span class="small">Ratting Saya</span>
                </div>
                <div class="card-body py-3">
                    <div class="row">
                        @forelse ($latestRattings as $item)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-warning shadow-sm">
                                    @php
                                        $coverFoto = null;
                                        if ($item->wisata) {
                                            $coverFoto = $item->wisata->foto()->where('is_cover', 1)->first();
                                        }
                                    @endphp
                                    @if ($coverFoto && $coverFoto->url)
                                        <img src="{{ asset('storage/' . $coverFoto->url) }}" class="card-img-top" alt="Foto {{ $item->wisata?->nama ?? '-' }}" style="height:150px;object-fit:cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height:150px;">
                                            <span class="text-muted small">Tidak ada foto</span>
                                        </div>
                                    @endif
                                    <div class="card-body py-2">
                                        <h6 class="card-title mb-1 small text-primary">{{ $item->wisata?->nama ?? '-' }}</h6>
                                        <div class="mb-1">
                                            <span class="badge bg-primary text-white">Ratting: {{ $item->ratting }}</span>
                                        </div>
                                        <div class="mb-2 text-muted small">
                                            {{ $item->ulasan ? \Illuminate\Support\Str::limit($item->ulasan, 60) : '-' }}
                                        </div>
                                        <a href="{{ route('rattings-wisatawan.show', $item) }}" class="btn btn-sm btn-outline-primary small">Detail</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info mb-0 small">Belum ada ratting.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <br>

            <div class="modal fade" id="photoPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            <img src="" alt="Foto" class="img-fluid w-100" id="photoPreviewImage">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-muted">Total Wisata</div>
                            <div class="h4 mb-0">{{ number_format($totalWisata ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-muted">Total Kategori</div>
                            <div class="h4 mb-0">{{ number_format($totalKategori ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-muted">Ratting Saya</div>
                            <div class="h4 mb-0">{{ number_format($totalRattingSaya ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-muted">Rata-rata Ratting</div>
                            <div class="h4 mb-0">
                                @if (!is_null($avgRattingSaya))
                                    {{ number_format($avgRattingSaya, 1) }}/5
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
@endsection
@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        #wisata-map {
            height: 360px;
            width: 100%;
        }
        .photo-scroll {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px;
            width: 230px;
        }
        .photo-scroll img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var mapElement = document.getElementById('wisata-map');
            if (!mapElement) {
                return;
            }
            var map = L.map('wisata-map');
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);
            var markers = [];
            var redIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            var greenIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            var orangeIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            function clearMarkers() {
                markers.forEach(function (marker) {
                    map.removeLayer(marker);
                });
                markers = [];
            }
            function updateMarkers() {
                clearMarkers();
                var points = [];
                // Data ratting saya dari backend
                var rattingSaya = window.rattingSaya || [];
                // Tambahkan marker dari hasil pencarian (checkbox)
                document.querySelectorAll('.js-wisata-checkbox:checked').forEach(function (checkbox) {
                    var lat = parseFloat(checkbox.getAttribute('data-lat'));
                    var lng = parseFloat(checkbox.getAttribute('data-lng'));
                    var name = checkbox.getAttribute('data-name') || '';
                    var photos = [];
                    try {
                        photos = JSON.parse(checkbox.getAttribute('data-photos') || '[]');
                    } catch (error) {
                        photos = [];
                    }
                    if (Number.isNaN(lat) || Number.isNaN(lng)) {
                        return;
                    }
                    // Cek apakah titik ini ada di ratting saya
                    var isRattingSaya = rattingSaya.some(function(item) {
                        return parseFloat(item.latitude) === lat && parseFloat(item.longitude) === lng;
                    });
                    var popupHtml = '<div><strong>' + name + '</strong>';
                    if (photos.length > 0) {
                        popupHtml += '<div class="mt-2 photo-scroll">';
                        photos.forEach(function (url) {
                            popupHtml += '<img src="' + url + '" alt="' + name + '" data-photo-url="' + url + '">';
                        });
                        popupHtml += '</div>';
                    } else {
                        popupHtml += '<div class="text-muted mt-1 small">Tidak ada foto.</div>';
                    }
                    popupHtml += '</div>';
                    var marker = L.marker([lat, lng], { icon: isRattingSaya ? orangeIcon : redIcon }).addTo(map).bindPopup(popupHtml);
                    markers.push(marker);
                    points.push([lat, lng]);
                });
                // Tambahkan marker dari rekomendasi (pakai icon hijau)
                if (window.rekomendasiWisata && Array.isArray(window.rekomendasiWisata)) {
                    window.rekomendasiWisata.forEach(function(item) {
                        var lat = parseFloat(item.latitude);
                        var lng = parseFloat(item.longitude);
                        var name = item.nama || '';
                        var photos = item.foto || [];
                        if (Number.isNaN(lat) || Number.isNaN(lng)) {
                            return;
                        }
                        var popupHtml = '<div><strong>' + name + '</strong>';
                        if (photos.length > 0) {
                            popupHtml += '<div class="mt-2 photo-scroll">';
                            photos.forEach(function (url) {
                                popupHtml += '<img src="' + url + '" alt="' + name + '" data-photo-url="' + url + '">';
                            });
                            popupHtml += '</div>';
                        } else {
                            popupHtml += '<div class="text-muted mt-1 small">Tidak ada foto.</div>';
                        }
                        popupHtml += '</div>';
                        var marker = L.marker([lat, lng], { icon: greenIcon }).addTo(map).bindPopup(popupHtml);
                        markers.push(marker);
                        points.push([lat, lng]);
                    });
                }
                if (points.length > 0) {
                    var bounds = L.latLngBounds(points);
                    map.fitBounds(bounds, { padding: [20, 20] });
                } else {
                    map.setView([-2.5489, 118.0149], 4);
                }
            }
            document.querySelectorAll('.js-wisata-checkbox').forEach(function (checkbox) {
                checkbox.addEventListener('change', updateMarkers);
            });
            map.on('popupopen', function (event) {
                var popupNode = event.popup.getElement();
                if (!popupNode) {
                    return;
                }
                var scrollContainer = popupNode.querySelector('.photo-scroll');
                if (scrollContainer) {
                    L.DomEvent.disableClickPropagation(scrollContainer);
                    L.DomEvent.disableScrollPropagation(scrollContainer);
                    scrollContainer.addEventListener('wheel', function (e) {
                        if (Math.abs(e.deltaX) < Math.abs(e.deltaY)) {
                            scrollContainer.scrollLeft += e.deltaY;
                            e.preventDefault();
                        }
                    }, { passive: false });
                }
                popupNode.querySelectorAll('img[data-photo-url]').forEach(function (img) {
                    img.addEventListener('click', function () {
                        var url = img.getAttribute('data-photo-url');
                        var previewImage = document.getElementById('photoPreviewImage');
                        if (previewImage && url) {
                            previewImage.src = url;
                            $('#photoPreviewModal').modal('show');
                        }
                    });
                });
            });
            // Data rekomendasi dari backend
            window.rekomendasiWisata = @json($rekomendasiWisata ?? []);
            // Data ratting saya dari backend
            window.rattingSaya = @json(
                ($latestRattings ?? collect([]))->map(function($item) {
                    return [
                        'latitude' => $item->wisata->latitude ?? null,
                        'longitude' => $item->wisata->longitude ?? null
                    ];
                })->values()
            );
            updateMarkers();
        });
    </script>
@endpush
