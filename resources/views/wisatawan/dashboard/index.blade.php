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
                                <div class="list-group-item d-flex align-items-center small">
                                    <input type="checkbox"
                                        class="mr-2 js-wisata-checkbox"
                                        aria-label="Pilih {{ $item->nama }}"
                                        data-lat="{{ $item->latitude }}"
                                        data-lng="{{ $item->longitude }}"
                                        data-name="{{ $item->nama }}"
                                        data-photos='@json($item->foto->map(function ($foto) { return asset("storage/" . $foto->url); })->values())'
                                        checked>
                                    <strong>{{ $item->nama }}</strong>
                                </div>
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

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Ratting Terbaru</span>
                    <a href="{{ route('rattings-wisatawan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Wisata</th>
                                    <th style="width: 120px;">Ratting</th>
                                    <th>Ulasan</th>
                                    <th style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestRattings as $item)
                                    <tr>
                                        <td>{{ $item->wisata?->nama ?? '-' }}</td>
                                        <td>{{ $item->ratting }}</td>
                                        <td>{{ $item->ulasan ? \Illuminate\Support\Str::limit($item->ulasan, 60) : '-' }}</td>
                                        <td>
                                            <a href="{{ route('rattings-wisatawan.show', $item) }}" class="btn btn-sm btn-info">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada ratting.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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

            function clearMarkers() {
                markers.forEach(function (marker) {
                    map.removeLayer(marker);
                });
                markers = [];
            }

            function updateMarkers() {
                clearMarkers();

                var points = [];
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

                    var marker = L.marker([lat, lng], { icon: redIcon }).addTo(map).bindPopup(popupHtml);
                    markers.push(marker);
                    points.push([lat, lng]);
                });

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

            updateMarkers();
        });
    </script>
@endpush
