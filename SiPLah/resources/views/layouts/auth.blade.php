<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Login &mdash; {{ optional($pengaturan)->name ?? config('app.name') }}</title>
    @include('includes.style')

    {{-- CSS KHUSUS UNTUK BACKGROUND GAMBAR PADA HALAMAN LOGIN SAJA --}}
    <style>
        body {
            /* Mengatur gambar sebagai latar belakang */
            background-image: url('https://png.pngtree.com/thumb_back/fw800/background/20230907/pngtree-blue-bubbles-and-soap-bubbles-wallpaper-image_13353811.jpg');
            background-size: cover; /* Memastikan gambar menutupi seluruh area */
            background-repeat: no-repeat; /* Mencegah gambar berulang */
            background-position: center center; /* Menyesuaikan posisi gambar di tengah */
            background-attachment: fixed; /* Membuat gambar tetap di tempat saat scroll */
            background-color: #6A5ACD; /* Warna cadangan jika gambar gagal dimuat */

            /* Penting: Jika ada elemen pembungkus seperti #app atau .section yang menutupi background body,
               pastikan mereka transparan agar background body dapat terlihat. */
        }

        #app, .section {
            background-color: transparent; /* Pastikan ini transparan */
        }
    </style>
    {{-- AKHIR CSS KHUSUS --}}

</head>

<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3">
                        <div class="login-brand">
                            <img src="{{ optional($pengaturan)->logo ? URL::asset($pengaturan->logo) : 'https://placehold.co/300x100/A0A0A0/FFFFFF?text=Logo' }}" alt="logo" width="100" class="shadow-lights">
                            {{-- Mengatur tulisan "SMA" menjadi tebal dan hitam --}}
                            <p class="mt-4" style="font-weight: bold; color: black;">{{ optional($pengaturan)->name ?? config('app.name') }}</p>
                        </div>
                        @if(session()->has('info'))
                        <div class="alert alert-primary">
                            {{ session()->get('info') }}
                        </div>
                        @endif
                        @if(session()->has('status'))
                        <div class="alert alert-info">
                            {{ session()->get('status') }}
                        </div>
                        @endif
                        @yield('content')
                    </div>
                </div>
            </div>
        </section>
    </div>
    @include('includes.script')
</body>
</html>
