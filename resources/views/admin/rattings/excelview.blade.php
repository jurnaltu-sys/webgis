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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}<br><small class="text-muted">{{ $user->email }}</small></td>
                        @php $userHasAnyRatting = false; @endphp
                        @foreach($wisataList as $wisata)
                            <td>
                                @php
                                    $ratting = $pivot[$user->id][$wisata->id] ?? null;
                                    if ($ratting !== null) $userHasAnyRatting = true;
                                @endphp
                                {{ $ratting !== null ? $ratting : '0' }}
                            </td>
                        @endforeach
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('rattings.exceluser.edit', $user->id) }}" class="btn btn-warning">{{ $userHasAnyRatting ? 'Edit' : 'Edit' }}</a>
                                @if($userHasAnyRatting)
                                <form action="{{ route('rattings.exceluser.delete', $user->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus semua ratting user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Reset</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
