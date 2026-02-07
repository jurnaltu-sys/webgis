@extends('layouts.app')

@section('content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('rattings-wisatawan.index') }}">Ratting Saya</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            Form Edit Ratting
        </div>
        <div class="card-body">
            <form action="{{ route('rattings-wisatawan.update', $ratting) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="wisata_id">Wisata</label>
                        <select name="wisata_id" id="wisata_id" class="form-control select2 @error('wisata_id') is-invalid @enderror" required>
                            <option value="">Pilih wisata</option>
                            @foreach ($wisata as $item)
                                <option value="{{ $item->id }}" {{ (string) old('wisata_id', $ratting->wisata_id) === (string) $item->id ? 'selected' : '' }}>
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
                                <option value="{{ $i }}" {{ (string) old('ratting', $ratting->ratting) === (string) $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('ratting')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="ulasan">Ulasan (opsional)</label>
                    <textarea name="ulasan" id="ulasan" rows="3" class="form-control @error('ulasan') is-invalid @enderror">{{ old('ulasan', $ratting->ulasan) }}</textarea>
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
        });
    </script>
@endpush
