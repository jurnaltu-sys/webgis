@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Ratting Saya</h1>
        <a href="{{ route('rattings-wisatawan.create') }}" class="btn btn-primary">Tambah</a>
    </div>

    <form method="GET" action="{{ route('rattings-wisatawan.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="q" value="{{ $query ?? '' }}" class="form-control" placeholder="Cari wisata atau ulasan...">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
                @if (!empty($query))
                    <a href="{{ route('rattings-wisatawan.index') }}" class="btn btn-outline-danger">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Wisata</th>
                            <th style="width: 120px;">Ratting</th>
                            <th>Ulasan</th>
                            <th style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rattings as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->wisata?->nama ?? '-' }}</td>
                                <td>{{ $item->ratting }}</td>
                                <td>{{ $item->ulasan ? \Illuminate\Support\Str::limit($item->ulasan, 60) : '-' }}</td>
                                <td>
                                    <a href="{{ route('rattings-wisatawan.show', $item) }}" class="btn btn-sm btn-info">Detail</a>
                                    <a href="{{ route('rattings-wisatawan.edit', $item) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('rattings-wisatawan.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($rattings->hasPages())
            <div class="card-footer">
                {{ $rattings->links() }}
            </div>
        @endif
    </div>
@endsection
