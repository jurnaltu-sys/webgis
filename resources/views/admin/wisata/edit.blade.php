@extends('layouts.app')

@section('content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('wisata.index') }}">Wisata</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            Form Edit Wisata
        </div>
        <div class="card-body">
            <form action="{{ route('wisata.update', $wisata) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-6">
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
                            <label for="kategori_id">Kategori</label>
                            <select name="kategori_id" id="kategori_id" class="form-control @error('kategori_id') is-invalid @enderror" required>
                                <option value="">Pilih kategori</option>
                                @foreach ($kategori as $item)
                                    <option value="{{ $item->id }}" {{ (string) old('kategori_id', $wisata->kategori_id) === (string) $item->id ? 'selected' : '' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
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
                            <label>Peta Lokasi</label>
                            <div id="map" class="rounded border"></div>
                        </div>

                        <div class="form-group">
                            <label for="jam_buka">Jam Buka (opsional)</label>
                            <input type="text" name="jam_buka" id="jam_buka" value="{{ old('jam_buka', $wisata->jam_buka) }}" class="form-control @error('jam_buka') is-invalid @enderror" maxlength="50">
                            @error('jam_buka')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi', $wisata->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="foto_wisata">Upload Gambar</label>
                            <div id="dropzone" class="border rounded p-4 text-center" style="cursor: pointer; border-style: dashed;">
                                <div class="text-muted">Drop gambar di sini atau klik untuk memilih</div>
                                <input type="file" name="foto_wisata[]" id="foto_wisata" class="d-none" accept="image/*" multiple>
                            </div>
                            <small class="form-text text-muted">Bisa upload banyak gambar sekaligus.</small>
                        </div>

                        @if ($wisata->foto && $wisata->foto->count())
                            <div class="mb-3">
                                <div class="text-muted mb-2">Gambar Saat Ini</div>
                                <div class="row">
                                    @foreach ($wisata->foto as $foto)
                                        <div class="col-6 mb-3">
                                            <div class="position-relative">
                                                <img
                                                    src="{{ asset('storage/' . $foto->url) }}"
                                                    alt="{{ $wisata->nama }}"
                                                    class="img-fluid rounded border"
                                                >
                                                <button
                                                    type="submit"
                                                    form="delete-foto-{{ $foto->id }}"
                                                    class="btn btn-sm btn-danger position-absolute"
                                                    style="top: 4px; right: 4px; z-index: 2;"
                                                    onclick="return confirm('Hapus gambar ini?')"
                                                >
                                                    &times;
                                                </button>
                                                <button
                                                    type="submit"
                                                    form="cover-foto-{{ $foto->id }}"
                                                    class="btn btn-sm btn-light position-absolute"
                                                    style="bottom: 6px; right: 6px; z-index: 2;"
                                                    @if ((int) $foto->is_cover === 1) disabled @endif
                                                >
                                                    @if ((int) $foto->is_cover === 1)
                                                        Cover
                                                    @else
                                                        Jadikan Cover
                                                    @endif
                                                </button>
                                                @if ((int) $foto->is_cover === 1)
                                                    <span class="badge badge-primary position-absolute" style="top: 6px; left: 6px;">Cover</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div id="preview" class="mb-3"></div>

                        <div class="form-group">
                            <label>Fasilitas</label>
                            <div class="border rounded p-3 @error('fasilitas') is-invalid @enderror">
                                @php
                                    $fasilitasOptions = ['Parkir', 'Toilet', 'Mushola', 'Restoran', 'Wifi', 'Penginapan'];
                                    $selectedFasilitas = old('fasilitas', $wisata->fasilitas ?? []);
                                @endphp
                                <div class="row">
                                    @foreach ($fasilitasOptions as $option)
                                        <div class="col-md-6">
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input" id="fasilitas_{{ $loop->index }}" name="fasilitas[]" value="{{ $option }}" {{ in_array($option, $selectedFasilitas, true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="fasilitas_{{ $loop->index }}">{{ $option }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('fasilitas')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
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
                    </div>
                </div>

                <div class="d-flex">
                    <a href="{{ route('wisata.index') }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>

            @if ($wisata->foto && $wisata->foto->count())
                @foreach ($wisata->foto as $foto)
                    <form
                        id="delete-foto-{{ $foto->id }}"
                        action="{{ route('wisata.foto.destroy', [$wisata, $foto]) }}"
                        method="POST"
                        class="d-none"
                    >
                        @csrf
                        @method('DELETE')
                    </form>
                    <form
                        id="cover-foto-{{ $foto->id }}"
                        action="{{ route('wisata.foto.cover', [$wisata, $foto]) }}"
                        method="POST"
                        class="d-none"
                    >
                        @csrf
                        @method('PATCH')
                    </form>
                @endforeach
            @endif
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
            var dropzone = document.getElementById('dropzone');
            var fileInput = document.getElementById('foto_wisata');
            var preview = document.getElementById('preview');

            var filesStore = [];

            function syncInputFiles() {
                var dataTransfer = new DataTransfer();
                filesStore.forEach(function (file) {
                    dataTransfer.items.add(file);
                });
                fileInput.files = dataTransfer.files;
            }

            function createPreviewItem(file, index) {
                var col = document.createElement('div');
                col.className = 'col-6 mb-3';

                var wrapper = document.createElement('div');
                wrapper.className = 'position-relative';

                var img = document.createElement('img');
                img.className = 'img-fluid rounded border';
                img.alt = file.name;

                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm btn-danger position-absolute';
                btn.style.top = '4px';
                btn.style.right = '4px';
                btn.innerHTML = '&times;';
                btn.addEventListener('click', function () {
                    filesStore.splice(index, 1);
                    renderPreviews();
                });

                var reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);

                wrapper.appendChild(img);
                wrapper.appendChild(btn);
                col.appendChild(wrapper);

                return col;
            }

            function renderPreviews() {
                preview.innerHTML = '';

                if (!filesStore.length) {
                    syncInputFiles();
                    return;
                }

                var row = document.createElement('div');
                row.className = 'row';

                filesStore.forEach(function (file, index) {
                    if (!file.type || file.type.indexOf('image/') !== 0) {
                        return;
                    }
                    row.appendChild(createPreviewItem(file, index));
                });

                preview.appendChild(row);
                syncInputFiles();
            }

            function appendFiles(files) {
                Array.prototype.forEach.call(files, function (file) {
                    if (!file.type || file.type.indexOf('image/') !== 0) {
                        return;
                    }
                    filesStore.push(file);
                });
                renderPreviews();
            }

            dropzone.addEventListener('click', function () {
                fileInput.click();
            });

            dropzone.addEventListener('dragover', function (e) {
                e.preventDefault();
                dropzone.classList.add('bg-light');
            });

            dropzone.addEventListener('dragleave', function () {
                dropzone.classList.remove('bg-light');
            });

            dropzone.addEventListener('drop', function (e) {
                e.preventDefault();
                dropzone.classList.remove('bg-light');

                if (e.dataTransfer && e.dataTransfer.files) {
                    appendFiles(e.dataTransfer.files);
                }
            });

            fileInput.addEventListener('change', function () {
                appendFiles(fileInput.files);
                fileInput.value = '';
            });
        })();

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

            var redIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var marker = L.marker([defaultLat, defaultLng], { icon: redIcon }).addTo(map);

            function updateMarker() {
                var lat = parseFloat(latInput.value);
                var lng = parseFloat(lngInput.value);

                if (!isNaN(lat) && !isNaN(lng)) {
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], map.getZoom());
                }
            }

            latInput.addEventListener('input', updateMarker);
            lngInput.addEventListener('input', updateMarker);
        })();
    </script>
@endsection
