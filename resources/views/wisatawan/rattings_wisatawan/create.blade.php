@extends('layouts.app')

@section('content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('rattings-wisatawan.index') }}">Ratting Saya</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
        </ol>
    </nav>

    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            Form Tambah Ratting
        </div>
        <div class="card-body">
            <form action="{{ route('rattings-wisatawan.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="wisata_id">Wisata</label>
                        <select name="wisata_id" id="wisata_id" class="form-control select2 @error('wisata_id') is-invalid @enderror" required>
                            <option value="">Pilih wisata</option>
                            @foreach ($wisata as $item)
                                @php
                                    $photos = $item->foto->map(function ($foto) {
                                        return asset('storage/' . $foto->url);
                                    })->values();
                                @endphp
                                <option
                                    value="{{ $item->id }}"
                                    data-photos='@json($photos)'
                                    {{ (string) old('wisata_id') === (string) $item->id ? 'selected' : '' }}
                                >
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('wisata_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ratting">Ratting</label>
                        <select name="ratting" id="ratting" class="form-control select2 @error('ratting') is-invalid @enderror" required>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ (string) old('ratting', 5) === (string) $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('ratting')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="d-block">Gambar Wisata</label>
                    <div class="border rounded p-2 bg-light">
                        <div
                            id="wisataPreviewStrip"
                            class="d-flex flex-row flex-nowrap"
                            style="gap: 12px; overflow-x: auto; padding-bottom: 6px;"
                        ></div>
                        <div id="wisataPreviewEmpty" class="text-muted text-center">Pilih wisata untuk melihat gambar.</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="ulasan">Ulasan (opsional)</label>
                    <textarea name="ulasan" id="ulasan" rows="3" class="form-control @error('ulasan') is-invalid @enderror">{{ old('ulasan') }}</textarea>
                    @error('ulasan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex">
                    <a href="{{ route('rattings-wisatawan.index') }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="wisataImageModal" tabindex="-1" role="dialog" aria-labelledby="wisataImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="wisataImageModalLabel">Gambar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="wisataImageModalImage" src="" alt="Gambar wisata" class="w-100 rounded" style="max-height:70vh; object-fit:contain;">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function () {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih',
                allowClear: true
            });

            function updateWisataPreview() {
                var selected = $('#wisata_id').find('option:selected');
                var photos = selected.data('photos');
                var previewStrip = document.getElementById('wisataPreviewStrip');
                var previewEmpty = document.getElementById('wisataPreviewEmpty');

                previewStrip.innerHTML = '';

                if (photos && photos.length) {
                    photos.forEach(function (url, index) {
                        var img = document.createElement('img');
                        img.src = url;
                        img.alt = 'Foto wisata ' + (index + 1);
                        img.className = 'img-fluid rounded';
                        img.style.height = '160px';
                        img.style.width = '240px';
                        img.style.objectFit = 'cover';
                        img.style.flex = '0 0 auto';
                        img.style.cursor = 'pointer';
                        img.addEventListener('click', function () {
                            var modalImage = document.getElementById('wisataImageModalImage');
                            modalImage.src = url;
                            $('#wisataImageModal').modal('show');
                        });
                        previewStrip.appendChild(img);
                    });
                    previewEmpty.style.display = 'none';
                    // Hide modal image when wisata changed
                    var modalImage = document.getElementById('wisataImageModalImage');
                    modalImage.src = '';
                } else {
                    previewEmpty.style.display = 'block';
                    var largeContainer = document.getElementById('wisataLargePreviewContainer');
                    var largeImage = document.getElementById('wisataLargePreviewImage');
                    largeImage.src = '';
                    largeContainer.style.display = 'none';
                }
            }

            $('#wisata_id').on('change', updateWisataPreview);
            updateWisataPreview();
        });
    </script>
@endpush
