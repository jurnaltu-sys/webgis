@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-3 d-flex justify-content-end align-items-center">
        <form method="GET" action="{{ route('wisatawan.rattings.dataset') }}" class="form-inline mr-2">
            <select name="user_id" id="user_id_select" class="form-control mr-2" style="min-width:220px">
                <option value="">-- Pilih User --</option>
                @foreach($users as $userOption)
                    <option value="{{ $userOption->id }}" @if(request('user_id') == $userOption->id) selected @endif>
                        {{ $userOption->name }} ({{ $userOption->email }})
                    </option>
                @endforeach
            </select>
            <button class="btn btn-primary" type="submit">Cari</button>
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
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                            <tr @if(auth()->id() == $user->id) style="background-color:#28a745;color:white" @endif>
                                <td>
                                    {{ $user->name }}<br>
                                    <small class="text-muted" @if(auth()->id() == $user->id) style="color:white !important" @endif>
                                        {{ $user->email }}
                                    </small>
                                </td>
                                @foreach($wisataList as $wisata)
                                    <td>
                                        {{ $pivot[$user->id][$wisata->id] ?? '0' }}
                                    </td>
                                @endforeach
                            </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
