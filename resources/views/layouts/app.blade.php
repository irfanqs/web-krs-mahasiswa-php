<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIAKAD Gallery') - SIAKAD Gallery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @stack('styles')
</head>
<body>
<div class="page-layout">
    @include('layouts.sidebar')

    <div class="page-main">
        @include('layouts.header')

        <main class="page-content">
            @if(session('success'))
                <div class="alert-flash alert-flash-success">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert-flash alert-flash-error">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

        @include('layouts.footer')
    </div>
</div>
@stack('scripts')
</body>
</html>
