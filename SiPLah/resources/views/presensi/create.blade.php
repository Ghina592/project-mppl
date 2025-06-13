@extends('layouts.main') {{-- Sesuaikan dengan layout utama Anda. Misal: layouts.app atau layouts.master --}}

@section('content')
<div class="container-fluid"> {{-- Gunakan container-fluid untuk lebar penuh --}}
    <h2 class="mb-4">Input Presensi Siswa</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi kesalahan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form filter berdasarkan Kelas dan Tanggal --}}
    <form action="{{ route('presensi.create') }}" method="GET" class="mb-4 p-3 border rounded shadow-sm bg-white">
        <div class="row g-3 align-items-end">
            <div class="col-md-4 col-sm-6">
                <label for="kelas_id" class="form-label">Pilih Kelas</label>
                <select name="kelas_id" id="kelas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ $selectedKelas == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 col-sm-6">
                <label for="tanggal" class="form-label">Tanggal Presensi</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control form-control-sm" value="{{ $selectedDate }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-4 col-sm-12 d-flex align-items-end">
                {{-- Tombol submit ini hanya sebagai fallback jika onchange tidak bekerja --}}
                <button type="submit" class="btn btn-primary btn-sm me-2 d-md-none">Filter</button>
                <a href="{{ route('presensi.create') }}" class="btn btn-secondary btn-sm">Reset Filter</a>
            </div>
        </div>
    </form>

    @if($selectedKelas && $siswas->isNotEmpty())
        {{-- Form untuk menyimpan presensi --}}
        <form action="{{ route('presensi.store') }}" method="POST">
            @csrf
            {{-- Input hidden untuk tanggal presensi yang dipilih --}}
            <input type="hidden" name="tanggal_presensi" value="{{ $selectedDate }}">

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th style="width: 15%;">NIS</th>
                            <th style="width: 25%;">Nama Siswa</th>
                            <th style="width: 15%;">Status Presensi</th>
                            <th style="width: 10%;">Jam Masuk</th>
                            <th style="width: 10%;">Jam Keluar</th>
                            <th style="width: 20%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $index => $siswa)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $siswa->nis }}</td>
                                <td>{{ $siswa->nama }}</td>
                                <td>
                                    @php
                                        // Mengambil data presensi yang sudah ada untuk siswa ini pada tanggal yang dipilih
                                        $currentStatus = $presensiData[$siswa->id]->status_presensi ?? 'Hadir';
                                        $currentJamMasuk = $presensiData[$siswa->id]->jam_masuk ?? null;
                                        $currentJamKeluar = $presensiData[$siswa->id]->jam_keluar ?? null;
                                        $currentKeterangan = $presensiData[$siswa->id]->keterangan ?? null;
                                    @endphp
                                    <select name="siswa[{{ $siswa->id }}][status_presensi]" class="form-select form-select-sm">
                                        <option value="Hadir" {{ $currentStatus == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                        <option value="Absen" {{ $currentStatus == 'Absen' ? 'selected' : '' }}>Absen</option>
                                        <option value="Izin" {{ $currentStatus == 'Izin' ? 'selected' : '' }}>Izin</option>
                                        <option value="Sakit" {{ $currentStatus == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                    </select>
                                </td>
                                <td><input type="time" name="siswa[{{ $siswa->id }}][jam_masuk]" class="form-control form-control-sm" value="{{ $currentJamMasuk ? \Carbon\Carbon::parse($currentJamMasuk)->format('H:i') : '' }}"></td>
                                <td><input type="time" name="siswa[{{ $siswa->id }}][jam_keluar]" class="form-control form-control-sm" value="{{ $currentJamKeluar ? \Carbon\Carbon::parse($currentJamKeluar)->format('H:i') : '' }}"></td>
                                <td><input type="text" name="siswa[{{ $siswa->id }}][keterangan]" class="form-control form-control-sm" value="{{ $currentKeterangan }}"></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Tidak ada siswa yang ditemukan di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div> {{-- End table-responsive --}}
            <div class="d-flex justify-content-between mt-3"> {{-- Menggunakan flexbox untuk meratakan tombol --}}
                <a href="{{ route('presensi.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Presensi
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Simpan Presensi
                </button>
            </div>
        </form>
    @elseif($selectedKelas)
        <div class="alert alert-info mt-4">Tidak ada siswa yang ditemukan di kelas yang dipilih ini.</div>
    @else
        <div class="alert alert-info mt-4">Silakan pilih kelas dan tanggal untuk menampilkan daftar siswa.</div>
    @endif
</div> {{-- End container-fluid --}}
@endsection