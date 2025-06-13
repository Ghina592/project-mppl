<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>@yield('title') | {{ $pengaturan->name ?? config('app.name', 'SiPLah App') }}</title>
    {{--
        Catatan: Variabel $pengaturan harus dikirim ke semua view yang menggunakan layout ini
        atau dibuat global via View Composer. Jika $pengaturan tidak selalu ada,
        gunakan default value seperti 'SiPLah App' untuk config('app.name').
    --}}

    {{-- Styling Utama (misalnya Bootstrap, Font Awesome, Stisla CSS) --}}
    @include('includes.style')
    {{-- Stack untuk CSS spesifik halaman --}}
    @stack('style')
</head>

<body>
    <div id="app">
        <div class="main-wrapper">
            <div class="navbar-bg"></div>
            {{-- Navbar --}}
            @include('partials.nav')

            {{-- Sidebar --}}
            @include('partials.sidebar')

            <div class="main-content">
                @yield('content')
            </div>

            {{-- Footer --}}
            @include('partials.footer')
        </div>
    </div>

    {{-- Scripts Utama (misalnya jQuery, Popper, Bootstrap JS, Stisla JS) --}}
    @include('includes.script')
    {{-- Stack untuk JavaScript spesifik halaman --}}
    @stack('script')
</body>
</html>