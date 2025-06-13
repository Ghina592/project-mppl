@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Dashboard</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-5">
                <div class="card profile-widget">
                    <div class="profile-widget-header">
                        {{-- Pastikan $siswa tidak null sebelum mencoba mengakses propertinya --}}
                        @if ($siswa)
                            @if ($siswa->foto) {{-- Cek apakah foto ada --}}
                                {{-- PERBAIKAN: Gunakan $siswa->foto langsung karena sudah berupa URL publik --}}
                                <img alt="image" src="{{ $siswa->foto }}" class="rounded-circle profile-widget-picture">
                            @else
                                <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle profile-widget-picture"> {{-- Default avatar jika tidak ada foto --}}
                            @endif
                        @else
                            {{-- Placeholder jika objek siswa null (misalnya, jika HomeController mengembalikan view ini setelah logout) --}}
                            <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle profile-widget-picture"> {{-- Default avatar --}}
                        @endif

                        <div class="profile-widget-items">
                            <div class="profile-widget-item">
                                <div class="profile-widget-item-label">NIS</div>
                                <div class="profile-widget-item-value">{{ $siswa->nis ?? '-' }}</div>
                            </div>
                            <div class="profile-widget-item">
                                <div class="profile-widget-item-label">Telp</div>
                                <div class="profile-widget-item-value">{{ $siswa->telp ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="profile-widget-description pb-0">
                        <div class="profile-widget-name">{{ $siswa->nama ?? 'Nama Siswa' }}
                            <div class="text-muted d-inline font-weight-normal">
                                <div class="slash"></div> siswa {{ $siswa->kelas?->nama_kelas ?? '-' }} {{-- Menggunakan nullsafe --}}
                            </div>
                        </div>
                        <label for="alamat">Alamat</label>
                        <p>{{ $siswa->alamat ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-3">
                <div class="card card-hero" style="margin-top: 36px">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h4>Pengumuman</h4>
                        <div class="card-description">Pengumuman sekolah hari ini</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="card-body p-0">
                            <div class="tickets-list">
                                @forelse ($pengumumans as $pengumuman)
                                <div class="ticket-item">
                                    <div class="ticket-title">
                                        <h4>{{ $pengumuman->description }}</h4>
                                    </div>
                                    <div class="ticket-info">
                                        <div class="text-muted">
                                            {{ \Carbon\Carbon::parse($pengumuman->created_at)->format('d F Y') }}
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="ticket-item">
                                    <div class="ticket-title">
                                        <h4>Tidak ada pengumuman hari ini</h4>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-3">
                <div class="card card-hero" style="margin-top: 36px">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <h4>Jadwal Mapel</h4>
                        {{-- Menggunakan $hariUntukView yang dikirim dari controller --}}
                        <div class="card-description">Jadwal Mapel hari ini ({{ $hariUntukView ?? 'N/A' }})</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="card-body p-0">
                            <div class="tickets-list">
                                {{-- Gunakan @forelse untuk jadwal. Query sudah memfilter hari ini di controller. --}}
                                @forelse ($jadwal as $data)
                                    <div class="ticket-item">
                                        <div class="ticket-title">
                                            {{-- Menampilkan nama mapel dari relasi --}}
                                            <h4>{{ $data->mapel->nama_mapel ?? 'Mata Pelajaran Tidak Ditemukan' }}</h4>
                                        </div>
                                        <div class="ticket-info">
                                            <div class="text-primary">
                                                Pada jam {{ \Carbon\Carbon::parse($data->dari_jam)->format('H:i') }} - {{ \Carbon\Carbon::parse($data->sampai_jam)->format('H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                    @if (!$loop->last)
                                        <hr class="my-2"> {{-- Tambahkan garis pemisah antar jadwal --}}
                                    @endif
                                @empty
                                    <div class="ticket-item">
                                        <div class="ticket-title">
                                            <h4>Tidak ada jadwal hari ini</h4>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-3">
                <div class="card card-hero" style="margin-top: 36px">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        {{-- Menampilkan jumlah materi --}}
                        <h4>{{ $materi->count() }}</h4>
                        <div class="card-description">Materi Tersedia</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="tickets-list">
                            @forelse ($materi as $data)
                            <div class="ticket-item">
                                <div class="ticket-title">
                                    <h4>{{ $data->judul }}</h4>
                                </div>
                                <div class="ticket-info">
                                    <div>{{ $data->guru?->nama ?? '-' }}</div>
                                    <div class="bullet"></div>
                                    <div class="text-primary">{{ $data->guru?->mapel?->nama_mapel ?? '-' }}</div>
                                </div>
                            </div>
                            @empty
                            <div class="ticket-item">
                                <div class="ticket-title">
                                    <h4>Tidak ada materi tersedia</h4>
                                </div>
                            </div>
                            @endforelse
                            <a href="{{ route('siswa.materi') }}" class="ticket-item ticket-more">
                                Lihat Semua <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-3">
                <div class="card card-hero" style="margin-top: 36px">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-clipboard-list"></i> {{-- Mengubah icon ke clipboard-list, lebih sesuai untuk tugas --}}
                        </div>
                        {{-- Menampilkan jumlah tugas --}}
                        <h4>{{ $tugas->count() }}</h4>
                        <div class="card-description">Tugas Tersedia</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="tickets-list">
                            @forelse ($tugas as $data)
                            <div class="ticket-item">
                                <div class="ticket-title">
                                    <h4>{{ $data->judul }}</h4>
                                </div>
                                <div class="ticket-info">
                                    <div>{{ $data->guru?->nama ?? '-' }}</div>
                                    <div class="bullet"></div>
                                    <div class="text-primary">{{ $data->guru?->mapel?->nama_mapel ?? '-' }}</div>
                                </div>
                            </div>
                            @empty
                            <div class="ticket-item">
                                <div class="ticket-title">
                                    <h4>Tidak ada tugas</h4>
                                </div>
                            </div>
                            @endforelse
                            <a href="{{ route('siswa.tugas') }}" class="ticket-item ticket-more"> {{-- Pastikan rute ini benar --}}
                                Lihat Semua <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
