@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h4 mb-1 text-success"><i class="fas fa-table mr-2"></i>Dataset Penilaian Wisatawan</h1>
        <p class="text-muted mb-0">Lihat dan ekspor data rating wisatawan untuk setiap destinasi wisata.</p>
    </div>
    <div class="row mb-3">
        <div class="col-md-8">
            <form method="GET" action="{{ route('wisatawan.rattings.dataset') }}" class="form-inline">
                <select name="user_id" id="user_id_select" class="form-control mr-2" style="min-width:220px">
                    <option value="">-- Pilih User --</option>
                    @foreach($users as $userOption)
                        <option value="{{ $userOption->id }}" @if(request('user_id') == $userOption->id) selected @endif>
                            {{ $userOption->name }} ({{ $userOption->email }})
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-outline-success" type="submit"><i class="fas fa-search mr-1"></i> Cari</button>
            </form>
        </div>
        <div class="col-md-4 text-right">
            <form method="GET" action="{{ route('wisatawan.rattings.dataset.export') }}" class="d-inline">
                <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-file-excel mr-1"></i> Export ke Excel
                </button>
            </form>
        </div>
    </div>
    <div class="card border-success shadow-sm bg-light">
        <div class="card-body">
            <h5 class="card-title mb-3 text-primary"><i class="fas fa-list-ol mr-1"></i>Ratting Format Dataset</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="align-middle">User</th>
                            @foreach($wisataList as $wisata)
                                <th class="align-middle">{{ $wisata->nama }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr @if(auth()->id() == $user->id) style="background-color:#fd7e14;color:white" @endif>
                                <td class="align-middle">
                                    <span class="font-weight-bold">{{ $user->name }}</span><br>
                                    <small class="text-muted" @if(auth()->id() == $user->id) style="color:white !important" @endif>
                                        {{ $user->email }}
                                    </small>
                                </td>
                                @foreach($wisataList as $wisata)
                                    <td class="align-middle text-center">
                                        {{ $pivot[$user->id][$wisata->id] ?? '0' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        .card {
            border-radius: 1rem;
        }
        .table thead th {
            background-color: #e9f7ef;
            color: #28a745;
        }
        .table-bordered td, .table-bordered th {
            border-color: #007bff;
        }
        .btn-outline-success {
            border-color: #28a745;
            color: #28a745;
        }
        .btn-outline-success:hover {
            background: #28a745;
            color: #fff;
        }
    </style>
@endpush
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
@endsection
