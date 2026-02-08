@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <a href="{{ route('rattings.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
    <h3 class="mb-4">Format Dataset</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th>User</th>
                    @foreach($wisataList as $wisata)
                        <th>{{ $wisata->nama }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}<br><small class="text-muted">{{ $user->email }}</small></td>
                        @foreach($wisataList as $wisata)
                            <td>
                                {{ $pivot[$user->id][$wisata->id] ?? '-' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
