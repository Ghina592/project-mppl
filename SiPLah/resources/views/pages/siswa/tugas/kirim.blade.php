@extends('layouts.main')
@section('title', 'Detail Tugas') {{-- Mengubah title agar lebih spesifik --}}

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Tugas: {{ $tugas->judul }}</h4> {{-- Judul tugas lebih jelas --}}
                            <a class="btn btn-primary btn-sm" href="{{ route('siswa.tugas') }}">Kembali ke Daftar Tugas</a> {{-- Mengarahkan kembali ke daftar tugas siswa --}}
                        </div>
                        <div class="card-body">
                            <h5>Deskripsi Tugas</h5>
                            <p>{{ $tugas->deskripsi }}</p>

                            <hr> {{-- Garis pemisah visual --}}

                            {{-- Bagian untuk mengumpulkan tugas --}}
                            <h5>Kumpulkan Tugas Anda</h5>

                            {{-- Menampilkan pesan sukses atau error dari sesi --}}
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Form untuk mengunggah tugas --}}
                            {{-- BARIS INI TELAH DIPERBAIKI: Menggunakan nama rute yang benar 'siswa.tugas.kumpul' --}}
                            <form action="{{ route('siswa.tugas.kumpul', $tugas->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf {{-- Token CSRF untuk keamanan Laravel --}}

                                <div class="form-group">
                                    <label for="file_tugas">Pilih File Tugas Anda:</label>
                                    <input type="file" class="form-control-file @error('file_tugas') is-invalid @enderror" id="file_tugas" name="file_tugas" required>
                                    @error('file_tugas')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">Format yang diizinkan: PDF, DOC, DOCX, PPT, PPTX, PNG, JPG, JPEG, ZIP, RAR (maks. 5MB).</small>
                                </div>

                                <div class="form-group">
                                    <label for="catatan_siswa">Catatan Tambahan (Opsional):</label>
                                    <textarea class="form-control" id="catatan_siswa" name="catatan_siswa" rows="3">{{ old('catatan_siswa', $submission->jawaban ?? '') }}</textarea>
                                    @error('catatan_siswa')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-success">Kumpulkan Tugas</button>
                            </form>

                            {{-- Menampilkan status pengumpulan tugas siswa --}}
                            <hr>
                            <h5>Status Pengumpulan Tugas</h5>
                            @if ($submission)
                                <p>Anda telah mengumpulkan tugas ini pada: {{ $submission->tanggal_kumpul ? \Carbon\Carbon::parse($submission->tanggal_kumpul)->format('d M Y H:i') : 'Tanggal tidak tersedia' }}</p>
                                <p>File yang diunggah: <a href="{{ route('jawaban.download', $submission->id) }}" target="_blank" class="btn btn-info btn-sm">Lihat File Jawaban</a></p>
                                @if ($submission->jawaban)
                                    <p>Catatan Anda: {{ $submission->jawaban }}</p>
                                @endif
                                <p class="text-success font-weight-bold">Status: Sudah Dikumpulkan</p>
                            @else
                                <p class="mt-3 text-danger font-weight-bold">Status: Belum Dikumpulkan</p>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
