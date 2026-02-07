<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'WebGIS' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    @stack('styles')
</head>
<body>
@unless (request()->routeIs('login') || request()->routeIs('register'))
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        @php
            $userRole = session('user_role');
        @endphp
        <a class="navbar-brand" href="{{ $userRole === 'wisatawan' ? route('dashboard-wisatawan.index') : route('wisata.index') }}">WebGIS</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                @if ($userRole === 'admin')
                    <li class="nav-item {{ request()->routeIs('wisata.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('wisata.index') }}">Wisata</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('kategori.index') }}">Kategori</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('users.index') }}">Users</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('rattings.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('rattings.index') }}">Ratting</a>
                    </li>
                @elseif ($userRole === 'wisatawan')
                    <li class="nav-item {{ request()->routeIs('dashboard-wisatawan.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard-wisatawan.index') }}">Dashboard</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('rattings-wisatawan.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('rattings-wisatawan.index') }}">Ratting Saya</a>
                    </li>
                @endif
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        User
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userMenu">
                        <h6 class="dropdown-header">{{ session('user_email', '-') }}</h6>
                        <span class="dropdown-item-text text-muted">Level: {{ session('user_role', '-') }}</span>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST" class="px-3">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger btn-block">Logout</button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
@endunless

<main class="container py-4">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
@stack('scripts')
</body>
</html>
