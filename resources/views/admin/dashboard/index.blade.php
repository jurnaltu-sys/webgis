@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body p-3">
                    <h6 class="text-primary"><i class="fas fa-search mr-2"></i>Pencarian Wisata</h6>
                    <form method="GET" action="{{ route('dashboard-admin.index') }}">
                        <div class="form-group">
                            <label for="search" class="sr-only">Cari</label>
                            <input type="text" name="q" id="search" class="form-control" placeholder="Cari nama wisata..." value="{{ $searchQuery ?? '' }}">
                        </div>
                        <button type="submit" class="btn btn-success btn-block"><i class="fas fa-search"></i> Cari</button>
                    </form>
                    <hr>
                    <h6 class="text-info mt-3"><i class="fas fa-map-marker-alt mr-2"></i>Hasil Pencarian</h6>
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
                                    <i class="fas fa-map-pin text-info mr-2"></i><strong class="mb-0">{{ $item->nama }}</strong>
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
                    <h1 class="h4 mb-1 text-success"><i class="fas fa-globe-asia mr-2"></i>Dashboard Admin</h1>
                    <p class="text-muted mb-0">Selamat datang, <span class="font-weight-bold text-primary">{{ session('user_email', '-') }}</span></p>
                </div>
            </div>
            <div class="card border-0 shadow-sm mb-4 bg-light">
                <div class="card-body p-0">
                    <div id="wisata-map"></div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm bg-info text-white">
                        <div class="card-body text-center">
                            <div class="text-white"><i class="fas fa-map-signs fa-2x mb-2"></i></div>
                            <div class="h6">Total Wisata</div>
                            <div class="h4 mb-0">{{ number_format($totalWisata ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm bg-success text-white">
                        <div class="card-body text-center">
                            <div class="text-white"><i class="fas fa-tags fa-2x mb-2"></i></div>
                            <div class="h6">Total Kategori</div>
                            <div class="h4 mb-0">{{ number_format($totalKategori ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm bg-dark text-white">
                        <div class="card-body text-center">
                            <div class="text-white"><i class="fas fa-users fa-2x mb-2"></i></div>
                            <div class="h6">Total User</div>
                            <div class="h4 mb-0">{{ number_format($totalUser ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
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
@endsection
@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
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
        .line-label {
            background-color: white;
            border: 1px solid black;
            border-radius: 4px;
            padding: 2px 4px;
            font-size: 12px;
            white-space: pre-line;
            min-width: 150px;
            text-align: center;
            max-width: 250px;
            word-wrap: break-word;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
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
            var connectorLines = []; // array of dashed segments connecting every pair
            var markerData = []; // array to store marker info: {marker, name, latlng}
            var redIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            // Function to calculate Euclidean distance (simplified for lat/lng)
            function euclideanDistance(lat1, lng1, lat2, lng2) {
                // Convert to approximate km: 1 degree lat ≈ 111 km, lng varies, use average
                const latDiffKm = (lat2 - lat1) * 111;
                const lngDiffKm = (lng2 - lng1) * 111 * Math.cos((lat1 + lat2) / 2 * Math.PI / 180);
                return Math.sqrt(latDiffKm * latDiffKm + lngDiffKm * lngDiffKm);
            }
            function clearMarkers() {
                markers.forEach(function (marker) {
                    map.removeLayer(marker);
                });
                markers = [];
                markerData = [];
                // also remove any existing connector lines
                connectorLines.forEach(function(line) {
                    map.removeLayer(line);
                });
                connectorLines = [];
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
                    markerData.push({marker: marker, name: name, latlng: [lat, lng]});
                    points.push([lat, lng]);
                });
                // Add click event to markers to show connecting lines with labels
                markerData.forEach(function(data) {
                    data.marker.on('click', function() {
                        // Clear existing connector lines
                        connectorLines.forEach(function(line) {
                            map.removeLayer(line);
                        });
                        connectorLines = [];
                        // Draw lines to all other markers with labels
                        markerData.forEach(function(otherData) {
                            if (otherData !== data) {
                                var distance = euclideanDistance(data.latlng[0], data.latlng[1], otherData.latlng[0], otherData.latlng[1]);
                                var label = data.name + ' - ' + otherData.name + '\nJarak: ' + distance.toFixed(2) + ' km';
                                var line = L.polyline([data.latlng, otherData.latlng], {
                                    color: 'blue',
                                    weight: 2,
                                    dashArray: '5,10'
                                }).addTo(map).bindTooltip(label, {
                                    permanent: true,
                                    direction: 'center',
                                    className: 'line-label'
                                });
                                connectorLines.push(line);
                            }
                        });
                    });
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
