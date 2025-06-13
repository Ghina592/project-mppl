@extends('layouts.main')
@section('title', 'Edit Pengumuman')

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center"> {{-- Tambahkan align-items-center --}}
                            <h4>Edit Pengumuman</h4>
                            {{-- PERBAIKAN RUTE: Ganti 'pengumuman-sekolah.index' menjadi 'admin.pengumuman-sekolah.index' --}}
                            <a href="{{ route('admin.pengumuman-sekolah.index') }}" class="btn btn-primary">Kembali</a>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')
                            {{-- PERBAIKAN RUTE: Ganti 'pengumuman-sekolah.update' menjadi 'admin.pengumuman-sekolah.update' --}}
                            <form method="POST" action="{{ route('admin.pengumuman-sekolah.update', $pengumuman->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="start_at">Tanggal Mulai</label>
                                    <input type="date" id="start_at" name="start_at"
                                        class="form-control @error('start_at') is-invalid @enderror"
                                        placeholder="Tanggal Mulai" value="{{ old('start_at', $pengumuman->start_at) }}">
                                    @error('start_at')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="end_at">Tanggal Selesai</label>
                                    <input type="date" id="end_at" name="end_at"
                                        class="form-control @error('end_at') is-invalid @enderror"
                                        placeholder="Tanggal Selesai" value="{{ old('end_at', $pengumuman->end_at) }}">
                                    @error('end_at')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="description">Deskripsi</label>
                                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Deskripsi pengumuman" rows="4">{{ old('description', $pengumuman->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="card-footer text-right"> {{-- Tambahkan text-right agar tombol di kanan --}}
                                    <button type="submit" class="btn btn-primary"><i class="nav-icon fas fa-save"></i>
                                        &nbsp; Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
{{-- Tidak ada push script tambahan yang diperlukan untuk input type="date" secara default --}}