
@extends('layouts.app')

@section('content')
    <style>
        body {
            background: url('/images/background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .tourism-login-card {
            background: rgba(255,255,255,0.95);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18);
            margin-top: 60px;
        }
        .tourism-header {
            background: linear-gradient(90deg, #1976d2 0%, #00b0ff 100%);
            color: #fff;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
            font-size: 1.5rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .tourism-icon {
            font-size: 2rem;
        }
        .btn-tourism {
            background: linear-gradient(90deg, #1976d2 0%, #00b0ff 100%);
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .btn-tourism:hover {
            background: linear-gradient(90deg, #00b0ff 0%, #1976d2 100%);
        }
        .wisata-footer {
            color: #636e72;
        }
    </style>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card tourism-login-card border-0">
                <div class="card-header tourism-header">
                    <span class="tourism-icon">🌴</span> Register Wisata Nusantara
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">Nama</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required autofocus placeholder="Masukkan nama Anda">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required placeholder="Masukkan email Anda">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Masukkan password Anda">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Ulangi password Anda">
                        </div>
                        <button type="submit" class="btn btn-tourism btn-block w-100">Daftar</button>
                    </form>
                    <div class="text-center mt-4 wisata-footer">
                        <small>Sudah punya akun? <a href="{{ route('login') }}" class="text-primary">Masuk</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
