@extends('layouts.main') {{-- Sesuaikan dengan layout utama Anda --}}

@section('title', 'Daftar Tugas Anda')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Daftar Tugas Anda</h4>
                        {{-- Tombol "Tambah Tugas" dihapus karena ini untuk siswa --}}
                    </div>
                    <div class="card-body">
                        {{-- Ini adalah tempat untuk menampilkan alert dari session --}}
                        @include('partials.alert')

                        {{-- Menampilkan pesan error jika siswa belum memiliki kelas --}}
                        @if(session('error_message'))
                            <div class="alert alert-warning">
                                {{ session('error_message') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped" id="table-tugas-siswa"> {{-- ID tabel diubah agar unik untuk siswa --}}
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul Tugas</th>
                                        <th>Deskripsi</th>
                                        <th>Tanggal Batas</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tugas as $data)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->judul }}</td>
                                        <td>{{ $data->deskripsi }}</td>
                                        {{-- Tangani jika tanggal_batas null --}}
                                        <td>{{ $data->tanggal_batas?->format('d M Y H:i') ?? 'N/A' }}</td>
                                        {{-- Tangani jika relasi guru.mapel null --}}
                                        <td>{{ $data->guru->mapel->nama_mapel ?? 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex">
                                                {{-- Tombol Download Soal/File --}}
                                                @if($data->file)
                                                    {{-- BARIS INI TELAH DIPERBAIKI: Menggunakan nama rute yang benar 'siswa.tugas.download' --}}
                                                    <a href="{{ route('siswa.tugas.download', $data->id) }}" class="btn btn-info btn-sm mr-2">
                                                        <i class="nav-icon fas fa-download"></i> Download Soal
                                                    </a>
                                                @else
                                                    <button class="btn btn-secondary btn-sm mr-2" disabled>
                                                        <i class="nav-icon fas fa-file-excel"></i> Tidak Ada File
                                                    </button>
                                                @endif

                                                {{-- Logika untuk Kirim/Edit Jawaban --}}
                                                @php
                                                    $isSubmitted = isset($jawaban[$data->id]);
                                                @endphp

                                                @if($isSubmitted)
                                                    <a href="{{ route('siswa.tugas.kumpul_form', $data->id) }}" class="btn btn-warning btn-sm mr-2">
                                                        <i class="nav-icon fas fa-edit"></i> Edit Jawaban
                                                    </a>
                                                    <span class="badge badge-success">Sudah Dikumpulkan</span>
                                                @else
                                                    <a href="{{ route('siswa.tugas.kumpul_form', $data->id) }}" class="btn btn-primary btn-sm mr-2">
                                                        <i class="nav-icon fas fa-upload"></i> Kirim Jawaban
                                                    </a>
                                                @endif

                                                {{-- Tombol "Lihat", "Edit", "Hapus" DIHAPUS untuk siswa --}}
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada tugas yang tersedia untuk kelas Anda.</td>
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

{{-- Script SweetAlert dan AJAX untuk Hapus tidak diperlukan di sini karena siswa tidak bisa menghapus --}}
{{-- @push('script') --}}
{{-- ... script Anda ... --}}
{{-- @endpush --}}
