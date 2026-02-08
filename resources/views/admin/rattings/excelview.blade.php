@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-3 d-flex justify-content-end align-items-center">
        <form method="GET" action="{{ route('rattings.excelview') }}" class="form-inline mr-2">
            <select name="user_id" id="user_id_select" class="form-control mr-2" style="min-width:220px">
                <option value="">-- Pilih User --</option>
                @foreach($users as $userOption)
                    <option value="{{ $userOption->id }}" @if(request('user_id') == $userOption->id) selected @endif>
                        {{ $userOption->name }} ({{ $userOption->email }})
                    </option>
                @endforeach
            </select>
            <button class="btn btn-primary" type="submit">Cari</button>
            @if(request('user_id'))
                <a href="{{ route('rattings.excelview') }}" class="btn btn-outline-danger ml-2">Delete</a>
            @endif
        </form>
        <a href="{{ route('rattings.import.form') }}" class="btn btn-secondary">Import</a>
        @push('scripts')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#user_id_select').select2({
                    placeholder: 'Cari user...',
                    allowClear: true
                });
            });
        </script>
        @endpush
    </div>
           
        </form>
        @push('scripts')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#user_id_select').select2({
                    placeholder: 'Cari user...',
                    allowClear: true
                });
            });
        </script>
        @endpush
    </div>
    <h3 class="mb-4">Ratting Format Dataset</h3>
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
