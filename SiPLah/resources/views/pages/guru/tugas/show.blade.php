@extends('layouts.main')
@section('title', 'Detail Pengumpulan Tugas') {{-- Mengubah title agar lebih spesifik --}}

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Pengumpulan Tugas: {{ $tugas->judul }}</h4>
                            <a class="btn btn-primary btn-sm" href="{{ route('tugas.index') }}">Kembali</a>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Siswa</th>
                                            <th>Kelas</th>
                                            <th>Tgl Pengumpulan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Iterasi langsung variabel $jawaban yang dikirim dari controller --}}
                                        @forelse ($jawaban as $data)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                {{-- Pastikan relasi siswa ada --}}
                                                <td>{{ $data->siswa->nama ?? 'N/A' }}</td>
                                                {{-- Pastikan relasi siswa.kelas ada --}}
                                                <td>{{ $data->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                                                <td>{{ $data->tanggal_kumpul ? date("d-m-Y H:i", strtotime($data->tanggal_kumpul)) : 'N/A' }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        {{-- Tombol Lihat Detail Jawaban (Opsional, jika ada halaman detail jawaban) --}}
                                                        {{-- <a href="{{ route('jawaban.show', $data->id) }}" class="btn btn-info btn-sm mr-2"><i class="nav-icon fas fa-eye"></i> Lihat Detail</a> --}}

                                                        {{-- BARIS INI TELAH DIPERBAIKI: Menggunakan nama rute yang benar 'jawaban.download' --}}
                                                        <a href="{{ route('jawaban.download', $data->id) }}" class="btn btn-success btn-sm">
                                                            <i class="nav-icon fas fa-download"></i> &nbsp; Download Jawaban
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Belum ada siswa yang mengumpulkan tugas ini.</td>
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
