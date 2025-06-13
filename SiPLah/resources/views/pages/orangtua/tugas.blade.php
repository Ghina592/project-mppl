@extends('layouts.main') {{-- Sesuaikan dengan layout utama Anda --}}

@section('title', 'Tugas Anak')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Tugas Anak</h4>
                        {{-- Tombol "Tambah Tugas" tidak ada di sini untuk orang tua --}}
                    </div>
                    <div class="card-body">
                        {{-- Ini adalah tempat untuk menampilkan alert dari session --}}
                        @include('partials.alert')

                        {{-- Menampilkan pesan error jika tidak ada siswa diasuh atau siswa belum memiliki kelas --}}
                        @if(isset($errorMessage))
                            <div class="alert alert-warning">
                                {{ $errorMessage }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped" id="table-tugas-anak"> {{-- ID tabel diubah agar unik --}}
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul Tugas</th>
                                        <th>Mapel</th>
                                        <th>Siswa</th> {{-- Kolom Siswa --}}
                                        <th>Tanggal Batas</th> {{-- BARIS PERBAIKAN: Menambahkan kolom Tanggal Batas --}}
                                        <th>Status Pengerjaan</th>
                                        <th>Tgl. Pengumpulan</th>
                                        <th>Hasil Kerja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tugas as $data) {{-- $data sekarang adalah objek Tugas --}}
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->judul }}</td>
                                        <td>{{ $data->guru->mapel->nama_mapel ?? 'N/A' }}</td>
                                        {{-- BARIS INI TELAH DIPERBAIKI: Mengakses nama siswa dari properti related_siswa yang ditambahkan di controller --}}
                                        <td>{{ $data->related_siswa->nama ?? 'N/A' }}</td>
                                        {{-- BARIS PERBAIKAN: Menampilkan Tanggal Batas --}}
                                        <td>
                                            @if ($data->tanggal_batas instanceof \Carbon\Carbon)
                                                {{ $data->tanggal_batas->format('d M Y H:i') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        @php
                                            // $data->id adalah tugas_id
                                            // $data->related_siswa->id adalah siswa_id dari anak yang mengerjakan tugas ini
                                            $submission = $jawabanSiswa->get($data->id); // Mengambil jawaban yang relevan untuk tugas ini
                                        @endphp

                                        <td>
                                            @if ($submission && $submission->siswa_id === $data->related_siswa->id)
                                                <span class="badge badge-success">Sudah Dikerjakan</span>
                                            @else
                                                <span class="badge badge-danger">Belum Dikerjakan</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($submission && $submission->siswa_id === $data->related_siswa->id && $submission->tanggal_kumpul)
                                                {{ \Carbon\Carbon::parse($submission->tanggal_kumpul)->format('d M Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($submission && $submission->siswa_id === $data->related_siswa->id && $submission->file)
                                                <a href="{{ route('jawaban.download', $submission->id) }}" class="btn btn-info btn-sm">
                                                    <i class="nav-icon fas fa-download"></i> Download
                                                </a>
                                            @else
                                                Belum Ada
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada tugas yang tersedia untuk anak Anda atau data siswa belum terdaftar.</td> {{-- PERBAIKAN: colspan menjadi 8 --}}
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
