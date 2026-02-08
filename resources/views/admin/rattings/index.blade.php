@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Data Ratting</h1>
        <div>
            <a href="{{ route('rattings.excelview') }}" target="_blank" class="btn btn-success mr-2">Format Dataset</a>
            <a href="{{ route('rattings.import.form') }}" class="btn btn-secondary mr-2">Import</a>
            <a href="{{ route('rattings.create') }}" class="btn btn-primary">Tambah</a>
        </div>
    </div>

    <form method="GET" action="{{ route('rattings.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="q" value="{{ $query ?? '' }}" class="form-control" placeholder="Cari...">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
                @if (!empty($query))
                    <a href="{{ route('rattings.index') }}" class="btn btn-outline-danger">Reset</a>
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
                            <th>User</th>
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
                                <td>{{ $item->user?->name ?? '-' }}</td>
                                <td>{{ $item->wisata?->nama ?? '-' }}</td>
                                <td>{{ $item->ratting }}</td>
                                <td>{{ $item->ulasan ? \Illuminate\Support\Str::limit($item->ulasan, 60) : '-' }}</td>
                                <td>
                                    <a href="{{ route('rattings.show', $item) }}" class="btn btn-sm btn-info">Detail</a>
                                    <a href="{{ route('rattings.edit', $item) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('rattings.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data.</td>
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
