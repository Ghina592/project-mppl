@extends('layouts.main') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
<div class="container-fluid"> {{-- Gunakan container-fluid untuk lebar penuh --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Presensi Siswa</h2>
        <a href="{{ route('presensi.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Input Presensi Baru
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 5%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 15%;">Nama Siswa</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 12%;">Mata Pelajaran</th>
                    <th class="text-center" style="width: 10%;">Status</th>
                    <th style="width: 8%;">Jam Masuk</th>
                    <th style="width: 8%;">Jam Keluar</th>
                    <th style="width: 15%;">Keterangan</th>
                    <th style="width: 10%;">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presensis as $index => $presensi)
                <tr>
                    <td class="text-center">{{ $presensis->firstItem() + $index }}</td> {{-- Untuk nomor urut di pagination --}}
                    <td>{{ \Carbon\Carbon::parse($presensi->tanggal_presensi)->format('d F Y') }}</td>
                    <td>{{ $presensi->siswa->nama ?? 'N/A' }}</td>
                    <td>{{ $presensi->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                    <td>{{ $presensi->mapel->nama_mapel ?? '-' }}</td>
                    <td class="text-center">
                        @if($presensi->status_presensi == 'Hadir')
                            <span class="badge bg-success">{{ $presensi->status_presensi }}</span>
                        @elseif($presensi->status_presensi == 'Absen')
                            <span class="badge bg-danger">{{ $presensi->status_presensi }}</span>
                        @elseif($presensi->status_presensi == 'Izin')
                            <span class="badge bg-info text-dark">{{ $presensi->status_presensi }}</span> {{-- badge-info butuh text-dark untuk kontras --}}
                        @elseif($presensi->status_presensi == 'Sakit')
                            <span class="badge bg-warning text-dark">{{ $presensi->status_presensi }}</span> {{-- badge-warning butuh text-dark untuk kontras --}}
                        @else
                            <span class="badge bg-secondary">{{ $presensi->status_presensi }}</span>
                        @endif
                    </td>
                    <td>{{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '-' }}</td>
                    <td>{{ $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') : '-' }}</td>
                    <td>{{ $presensi->keterangan ?? '-' }}</td>
                    <td>{{ $presensi->guru->nama ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted">Belum ada data presensi yang tercatat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div> {{-- End table-responsive --}}

    <div class="d-flex justify-content-between mt-3">
        {{-- Tombol Kembali --}}
        <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard Guru
        </a>
        {{ $presensis->links() }} {{-- Untuk pagination --}}
    </div>
</div> {{-- End container-fluid --}}
@endsection