@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Input/Edit Semua Ratting untuk {{ $user->name }} <small class="text-muted">({{ $user->email }})</small></h3>
    <form action="{{ route('rattings.exceluser.update', $user->id) }}" method="POST">
        @csrf
        <div class="table-responsive mb-3">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Wisata</th>
                        <th>Nilai Ratting (1-5)</th>
                        <th>Ulasan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wisataList as $wisata)
                        <tr>
                            <td>{{ $wisata->nama }}</td>
                            <td>
                                <input type="number" name="ratting[{{ $wisata->id }}]" class="form-control form-control-sm" min="0" max="5" value="{{ old('ratting.' . $wisata->id, $rattings[$wisata->id]->ratting ?? '') }}" placeholder="0">
                                <small class="text-muted">0 = kosong/tidak ada</small>
                            </td>
                            <td>
                                <input type="text" name="ulasan[{{ $wisata->id }}]" class="form-control form-control-sm" value="{{ old('ulasan.' . $wisata->id, $rattings[$wisata->id]->ulasan ?? '') }}" placeholder="Ulasan (opsional)">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Simpan Semua</button>
            <a href="{{ route('rattings.excelview') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
