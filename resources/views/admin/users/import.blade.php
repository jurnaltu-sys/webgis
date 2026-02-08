@extends('layouts.app')

@section('content')
    <div class="mb-3">
        <h1 class="h4">Import Users dari Excel</h1>
        <p class="text-muted">Pastikan file memiliki header: <strong>name, email, password, role</strong>.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Pilih file Excel</label>
            <input type="file" name="file" id="file" class="form-control-file" accept=".xlsx,.xls,.csv" required>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Upload & Import</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>

@endsection
