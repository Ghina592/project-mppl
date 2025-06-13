@extends('layouts.main') {{-- Menggunakan layout yang Anda sebutkan --}}
@section('title', 'Dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard Orang Tua</h1>
        </div>

        <div class="section-body">
            {{-- Bagian untuk menampilkan daftar siswa yang terkait dengan orang tua --}}
            <div class="row">
                @forelse($orangtua->siswas as $key => $siswa)
                    <div class="col-12 col-sm-12 col-lg-4">
                        <div class="card profile-widget">
                            <div class="profile-widget-header">
                                {{-- Cek apakah foto siswa ada, jika tidak gunakan placeholder --}}
                                @if ($siswa->foto)
                                    {{-- Pastikan jalur foto benar. Jika foto disimpan di 'images/siswa' dalam public disk --}}
                                    <img alt="image" src="{{ Storage::url($siswa->foto) }}"
                                         class="rounded-circle profile-widget-picture">
                                @else
                                    <img alt="image" src="https://placehold.co/300x300/e0e0e0/ffffff?text=No+Photo"
                                         class="rounded-circle profile-widget-picture">
                                @endif
                                <div class="profile-widget-items">
                                    {{-- Perbaikan di sini: Menggabungkan NIS dan Telp dalam satu profile-widget-item --}}
                                    <div class="profile-widget-item">
                                        <div class="profile-widget-item-label">NIS</div>
                                        <div class="profile-widget-item-value">{{ $siswa->nis ?? '-' }}</div>
                                        {{-- Menambahkan margin-top untuk jarak antara NIS dan Telp --}}
                                        <div class="profile-widget-item-label" style="margin-top: 5px;">Telp</div>
                                        <div class="profile-widget-item-value">{{ $siswa->telp ?? '-' }}</div>
                                    </div>
                                    {{-- Menghapus div profile-widget-item Telp yang terpisah --}}
                                </div>
                            </div>
                            <div class="profile-widget-description pb-0">
                                {{-- Menggunakan null coalescing operator untuk nama siswa --}}
                                <div class="profile-widget-name">{{ $siswa->nama ?? 'Nama Siswa' }}
                                    <div class="text-muted d-inline font-weight-normal">
                                        <div class="slash"></div> siswa
                                        {{-- Menggunakan nullsafe operator untuk kelas dan nama_kelas --}}
                                        {{ $siswa->kelas?->nama_kelas ?? '-' }}
                                    </div>
                                </div>
                                <label for="alamat">Alamat</label>
                                {{-- Menggunakan null coalescing operator untuk alamat siswa --}}
                                <p>{{ $siswa->alamat ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Pesan jika tidak ada siswa yang terkait dengan orang tua ini --}}
                    <div class="col-12">
                        <div class="alert alert-warning" role="alert">
                            Tidak ada siswa yang terdaftar untuk akun orang tua ini.
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Bagian untuk menampilkan data Presensi Siswa --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-clipboard-check me-2"></i> Riwayat Presensi Anak-anak Anda</h4>
                        </div>
                        <div class="card-body p-0">
                            @if($presensis->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-striped table-md">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Anak</th>
                                            <th>Tanggal</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Status</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Keterangan</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($presensis as $index => $presensi)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $presensi->siswa->nama ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($presensi->tanggal_presensi)->format('d F Y') }}</td>
                                                <td>{{ $presensi->mapel->nama_mapel ?? '-' }}</td>
                                                <td>
                                                    @if($presensi->status_presensi == 'Hadir')
                                                        <span class="badge bg-success">Hadir</span>
                                                    @elseif($presensi->status_presensi == 'Absen')
                                                        <span class="badge bg-danger">Absen</span>
                                                    @elseif($presensi->status_presensi == 'Izin')
                                                        <span class="badge bg-info text-dark">Izin</span>
                                                    @elseif($presensi->status_presensi == 'Sakit')
                                                        <span class="badge bg-warning text-dark">Sakit</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $presensi->status_presensi }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '-' }}</td>
                                                <td>{{ $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') : '-' }}</td>
                                                <td>{{ $presensi->keterangan ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty-state" data-height="200">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    <h2>Tidak ada data presensi yang tercatat untuk anak-anak Anda.</h2>
                                    <p class="lead">Pastikan data presensi sudah diinput oleh guru.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Pengumuman Sekolah --}}
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-6"> {{-- Diubah ke col-lg-6 agar lebih lebar --}}
                    <div class="card card-hero" style="margin-top: 36px">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <h4>Pengumuman</h4>
                            <div class="card-description">Pengumuman sekolah hari ini</div>
                        </div>
                        <div class="card-body p-0">
                            <div class="tickets-list">
                                @forelse ($pengumumans as $pengumuman)
                                    <div class="ticket-item">
                                        <div class="ticket-title">
                                            <h4>{{ $pengumuman->description }}</h4>
                                        </div>
                                        <div class="ticket-info">
                                            <div>Tanggal: {{ \Carbon\Carbon::parse($pengumuman->start_at)->format('d M Y') }} - {{ \Carbon\Carbon::parse($pengumuman->end_at)->format('d M Y') }}</div>
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

                {{-- Bagian Jadwal Mapel, Materi Tersedia, Tugas Tersedia (jika ingin diaktifkan kembali) --}}
                {{-- Anda bisa uncomment bagian ini jika ingin menampilkannya di dashboard orang tua.
                     Pastikan data ($jadwal, $materi, $tugas) juga dilewatkan dari OrangtuaDashboardController.
                     Untuk saat ini, saya biarkan bagian ini tetap dikomentari agar fokus pada presensi.
                 --}}
                {{-- <div class="col-12 col-sm-12 col-lg-3">
                    <div class="card card-hero" style="margin-top: 36px">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <h4>Jadwal Mapel</h4>
                            <div class="card-description">Jadwal Mapel hari ini</div>
                        </div>
                        <div class="card-body p-0">
                            <div class="tickets-list">
                                @foreach ($jadwal as $data)
                                    @if($data->hari == $hari)
                                        <div class="ticket-item">
                                            <div class="ticket-title">
                                                <h4>{{ $data->kelas->nama_kelas }}</h4>
                                            </div>
                                            <div class="ticket-info">
                                                <div class="text-primary">Pada jam {{ $data->dari_jam }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="ticket-item">
                                            <div class="ticket-title">
                                                <h4>Tidak ada jadwal hari ini</h4>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="col-12 col-sm-12 col-lg-3">
                    <div class="card card-hero" style="margin-top: 36px">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <h4>{{ $materi->count() }}</h4>
                            <div class="card-description">Materi Tersedia</div>
                        </div>
                        <div class="card-body p-0">
                            <div class="tickets-list">
                                @foreach ($materi as $data )
                                    @if($data->count() > 0)
                                    <div class="ticket-item">
                                        <div class="ticket-title">
                                            <h4>{{ $data->judul }}</h4>
                                        </div>
                                        <div class="ticket-info">
                                            <div>{{ $data->guru->nama }}</div>
                                            <div class="bullet"></div>
                                            <div class="text-primary">{{ $data->guru->mapel->nama_mapel }}</div>
                                        </div>
                                    </div>
                                    <a href="{{ route('siswa.materi') }}" class="ticket-item ticket-more">
                                        Lihat Semua <i class="fas fa-chevron-right"></i>
                                    </a>
                                    @else
                                    <div class="ticket-item">
                                        <div class="ticket-title">
                                            <h4>Tidak ada materi tersedia</h4>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="col-12 col-sm-12 col-lg-3">
                    <div class="card card-hero" style="margin-top: 36px">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <h4>{{ $tugas->count() }}</h4>
                            <div class="card-description">Tugas Tersedia</div>
                        </div>
                        <div class="card-body p-0">
                            <div class="tickets-list">
                                @foreach ($tugas as $data )
                                    @if ($data->count() > 0)
                                    <div class="ticket-item">
                                        <div class="ticket-title">
                                            <h4>{{ $data->judul }}</h4>
                                        </div>
                                        <div class="ticket-info">
                                            <div>{{ $data->guru->nama }}</div>
                                            <div class="bullet"></div>
                                            <div class="text-primary">{{ $data->guru->mapel->nama_mapel }}</div>
                                        </div>
                                    </div>
                                    <a href="{{ route('siswa.materi') }}" class="ticket-item ticket-more">
                                        Lihat Semua <i class="fas fa-chevron-right"></i>
                                    </a>
                                    @else
                                    <div class="ticket-item">
                                        <div class="ticket-title">
                                            <h4>Tidak ada tugas</h4>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div> --}}

            </div>
        </div>
    </section>
@endsection