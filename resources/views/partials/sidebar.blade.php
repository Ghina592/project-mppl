<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand mt-3">
            {{-- Menggunakan null coalescing operator dan placeholder yang lebih baik untuk logo dan nama aplikasi --}}
            <img src="{{ URL::asset($pengaturan->logo ?? 'https://placehold.co/300x300/e0e0e0/ffffff?text=Logo') }}" alt="Logo" style="width: 50px">
            <a href="">{{ $pengaturan->name ?? config('app.name') }}</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#">{{ strtoupper(substr(config('app.name'), 0, 2)) }}</a>
        </div>
        <ul class="sidebar-menu">
            {{-- Admin Menu --}}
            @if (Auth::check() && Auth::user()->roles == 'admin')
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-columns"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="menu-header">Master Data</li>

                <li class="{{ request()->routeIs('admin.jurusan.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.jurusan.index') }}">
                        <i class="fas fa-book"></i> <span>Jurusan</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.mapel.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.mapel.index') }}">
                        <i class="fas fa-book"></i> <span>Mata Pelajaran</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.guru.index') }}">
                        <i class="fas fa-user"></i> <span>Guru</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.kelas.index') }}">
                        <i class="far fa-building"></i> <span>Kelas</span>
                    </a>
                </li>

                {{-- Workaround JavaScript untuk link Siswa (agar bisa diklik) --}}
                {{-- Ini adalah solusi sementara untuk mengatasi masalah klik yang tidak merespon --}}
                <li class="{{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                    <a class="nav-link" href="#" onclick="window.location.href = '{{ route('admin.siswa.index') }}'; return false;">
                        <i class="fas fa-users"></i> <span>Siswa</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.jadwal.index') }}">
                        <i class="fas fa-calendar"></i> <span>Jadwal</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.user.index') }}">
                        <i class="fas fa-user"></i> <span>User</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.pengumuman-sekolah.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.pengumuman-sekolah.index') }}">
                        <i class="fas fa-bullhorn"></i> <span>Pengumuman</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.pengaturan.index') }}">
                        <i class="fas fa-cog"></i> <span>Pengaturan</span>
                    </a>
                </li>

                {{-- Jika admin juga perlu akses presensi (opsional, uncomment jika diperlukan) --}}
                {{--
                <li class="dropdown {{ request()->routeIs('presensi.*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-calendar-check"></i> <span>Presensi</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ request()->routeIs('presensi.create') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('presensi.create') }}">Input Presensi</a>
                        </li>
                        <li class="{{ request()->routeIs('presensi.index') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('presensi.index') }}">Lihat Presensi</a>
                        </li>
                    </ul>
                </li>
                --}}

            {{-- Guru Menu --}}
            @elseif (Auth::check() && Auth::user()->roles == 'guru')
                <li class="{{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('guru.dashboard') }}">
                        <i class="fas fa-columns"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="menu-header">Master Data</li>
                <li class="{{ request()->routeIs('materi.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('materi.index') }}">
                        <i class="fas fa-book"></i> <span>Materi</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('tugas.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('tugas.index') }}">
                        <i class="fas fa-list"></i> <span>Tugas</span>
                    </a>
                </li>
                {{-- --- Tambahkan menu presensi di sini untuk Guru --- --}}
                <li class="dropdown {{ request()->routeIs('presensi.*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-calendar-check"></i> <span>Presensi</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ request()->routeIs('presensi.create') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('presensi.create') }}">Input Presensi</a>
                        </li>
                        <li class="{{ request()->routeIs('presensi.index') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('presensi.index') }}">Lihat Presensi</a>
                        </li>
                    </ul>
                </li>

            {{-- Siswa Menu --}}
            @elseif (Auth::check() && Auth::user()->roles == 'siswa')
                <li class="{{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('siswa.dashboard') }}">
                        <i class="fas fa-columns"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('siswa.materi') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('siswa.materi') }}">
                        <i class="fas fa-book"></i> <span>Materi</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('siswa.tugas') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('siswa.tugas') }}">
                        <i class="fas fa-list"></i> <span>Tugas</span>
                    </a>
                </li>

            {{-- Orang Tua Menu (Bagian else terakhir untuk role lain atau default) --}}
            @else {{-- Ini akan mencakup role 'orangtua' dan role lainnya jika ada --}}
                <li class="{{ request()->routeIs('orangtua.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('orangtua.dashboard') }}">
                        <i class="fas fa-columns"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('orangtua.tugas.siswa') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('orangtua.tugas.siswa') }}">
                        <i class="fas fa-list"></i> <span>Tugas Anak</span>
                    </a>
                </li>
                {{-- Presensi anak sudah ada di dashboard orang tua, jadi tidak perlu menu terpisah di sidebar --}}
            @endif

        </ul>
    </aside>
</div>