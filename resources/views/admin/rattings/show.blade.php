@extends('layouts.app')

@section('content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('rattings.index') }}">Ratting</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail</li>
        </ol>
    </nav>

    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            Detail Ratting
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>ID</label>
                    <input type="text" class="form-control" value="{{ $ratting->id }}" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>User</label>
                    <input type="text" class="form-control" value="{{ $ratting->user?->name ?? '-' }}" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Wisata</label>
                    <input type="text" class="form-control" value="{{ $ratting->wisata?->nama ?? '-' }}" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Ratting</label>
                    <input type="text" class="form-control" value="{{ $ratting->ratting }}" readonly>
                </div>
                <div class="form-group col-md-8">
                    <label>Ulasan</label>
                    <textarea class="form-control" rows="3" readonly>{{ $ratting->ulasan ?? '-' }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('rattings.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
@endsection
