@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Selamat Datang </h1>
    </div>

    <div class="section-body">
        <div class="row ">
            {{-- Jadwal --}}
            <div class="col-12 col-sm-12 col-lg-4">
                <div class="card card-hero">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h4>Jadwal Mengajar Hari Ini ({{ $hariUntukView ?? 'Tidak Diketahui' }})</h4> {{-- Perbaikan 1: Tambahkan $hariUntukView di sini --}}
                        <div class="card-description">Berikut list jadwal kelas tempat mengajar Anda pada hari ini.</div> {{-- Deskripsi disesuaikan --}}
                    </div>
                    <div class="card-body p-0">
                        <div class="tickets-list">
                            @forelse ($jadwal as $data ) {{-- Menggunakan @forelse untuk menangani jika tidak ada jadwal --}}
                            {{-- Karena jadwal sudah difilter di controller berdasarkan hari ini,
                                 kita tidak perlu lagi kondisi @if($data->hari == $hari) di sini.
                                 Semua $data dalam loop ini sudah pasti jadwal hari ini. --}}
                            <div class="ticket-item">
                                <div class="ticket-title">
                                    <h4>{{ $data->mapel->nama_mapel }} - Kelas {{ $data->kelas->nama_kelas }}</h4> {{-- Tampilkan nama mapel dan kelas --}}
                                </div>
                                <div class="ticket-info">
                                    <div class="text-primary">Pada jam {{ \Carbon\Carbon::parse($data->dari_jam)->format('H:i') }} - {{ \Carbon\Carbon::parse($data->sampai_jam)->format('H:i') }}</div> {{-- Format jam lebih rapi --}}
                                </div>
                            </div>
                            @empty
                            <div class="ticket-item">
                                <div class="ticket-title">
                                    <h4>Tidak ada jadwal mengajar hari ini.</h4>
                                </div>
                            </div>
                            @endforelse {{-- Tutup @forelse --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Jumlah Materi Diajarkan</h4>
                        </div>
                        <div class="card-body">
                            {{ $materi }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Jumlah Tugas diberikan</h4>
                        </div>
                        <div class="card-body">
                            {{ $tugas }}
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</section>
@endsection