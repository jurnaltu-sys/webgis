@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Data Wisata</h1>
        <div>
            <a href="{{ route('wisata.import.form') }}" class="btn btn-secondary mr-2">Import Excel</a>
            <a href="{{ route('wisata.create') }}" class="btn btn-primary">Tambah</a>
        </div>
    </div>

    <form method="GET" action="{{ route('wisata.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="q" value="{{ $query ?? '' }}" class="form-control" placeholder="Cari nama wisata...">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
                @if (!empty($query))
                    <a href="{{ route('wisata.index') }}" class="btn btn-outline-danger">Reset</a>
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
                            <th style="width: 80px;">No</th>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Kategori</th>
                            <th>Rating</th>
                            <th style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = ($wisata instanceof \Illuminate\Pagination\LengthAwarePaginator) ? ($wisata->firstItem() ?? 1) : 1; @endphp
                        @forelse ($wisata as $item)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->slug }}</td>
                                <td>{{ $item->kategori->nama ?? '-' }}</td>
                                <td>{{ number_format((float) $item->rating_avg, 2) }} ({{ $item->jml_rating }})</td>
                                <td>
                                    <a href="{{ route('wisata.show', $item) }}" class="btn btn-sm btn-info">Detail</a>
                                    <a href="{{ route('wisata.edit', $item) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('wisata.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
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
        @if ($wisata->hasPages())
            <div class="card-footer">
                {{ $wisata->links() }}
            </div>
        @endif
    </div>
@endsection
