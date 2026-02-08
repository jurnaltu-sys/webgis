@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Import Rattings</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('import_errors'))
            <div class="alert alert-warning">
                <strong>Beberapa baris tidak diproses:</strong>
                <ul class="mb-0">
                    @foreach(session('import_errors') as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                </ul>
                <div class="small text-muted mt-2">Silakan periksa email user atau nama wisata pada baris yang disebutkan.</div>
            </div>
        @endif

        <form action="{{ route('rattings.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="file">Excel file</label>
                <input type="file" name="file" id="file" class="form-control" required accept=".xlsx,.xls,.csv">
            </div>
            <button class="btn btn-primary mt-3" type="submit">Import</button>
        </form>
    </div>
@endsection
