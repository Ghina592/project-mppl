@extends('layouts.main')
@section('title', 'Edit Kelas')

@push('style')
    {{-- Memuat CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- Jika menggunakan tema Bootstrap 4 untuk Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    <style>
        .form-group label {
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                @include('partials.alert')
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center"> {{-- Tambahkan align-items-center --}}
                        <h4>Edit Kelas {{ $kelas->nama_kelas }}</h4>
                        {{-- PERBAIKAN RUTE: Ganti 'kelas.index' menjadi 'admin.kelas.index' --}}
                        <a href="{{ route('admin.kelas.index') }}" class="btn btn-primary">Kembali</a>
                    </div>
                    <div class="card-body">
                        {{-- PERBAIKAN RUTE: Ganti 'kelas.update' menjadi 'admin.kelas.update' --}}
                        <form method="POST" action="{{ route('admin.kelas.update', $kelas->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="nama_kelas">Nama Kelas</label>
                                <input type="text" id="nama_kelas" name="nama_kelas" class="form-control @error('nama_kelas') is-invalid @enderror"
                                    placeholder="Nama Kelas" value="{{ old('nama_kelas', $kelas->nama_kelas) }}">
                                @error('nama_kelas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="jurusan_id">Jurusan</label>
                                <select id="jurusan_id" name="jurusan_id" class="form-control select2bs4 @error('jurusan_id') is-invalid @enderror"> {{-- Gunakan select2bs4 --}}
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach ($jurusan as $item)
                                        <option value="{{ $item->id }}"
                                            {{ (string)(old('jurusan_id') ?? $kelas->jurusan_id) === (string)$item->id ? 'selected' : '' }}> {{-- Gunakan old() dan casting ke string --}}
                                            {{ $item->nama_jurusan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jurusan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="guru_id">Wali Kelas</label>
                                <select id="guru_id" name="guru_id" class="form-control select2bs4 @error('guru_id') is-invalid @enderror"> {{-- Gunakan select2bs4 --}}
                                    <option value="">-- Pilih Wali Kelas --</option>
                                    @foreach ($guru as $item)
                                        {{-- Asumsi $item->nama adalah nama guru yang ingin ditampilkan --}}
                                        <option value="{{ $item->id }}"
                                            {{ (string)(old('guru_id') ?? $kelas->guru_id) === (string)$item->id ? 'selected' : '' }}> {{-- Gunakan old() dan casting ke string --}}
                                            {{ $item->user->name ?? $item->nama }} {{-- Tampilkan nama dari user jika ada, atau nama guru langsung --}}
                                        </option>
                                    @endforeach
                                </select>
                                @error('guru_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="card-footer text-right"> {{-- Tambahkan text-right agar tombol di kanan --}}
                                <button type="submit" class="btn btn-primary">
                                    <i class="nav-icon fas fa-save"></i> &nbsp; Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
    {{-- Memuat JavaScript Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Inisialisasi Select2
            $('.select2bs4').select2({ // Gunakan select2bs4
                theme: 'bootstrap4', // Pastikan tema ini diatur jika Anda menggunakan select2-bootstrap4-theme
                placeholder: "-- Pilih Data --", // Placeholder umum untuk dropdown
                allowClear: true // Opsi ini memungkinkan pengguna untuk menghapus pilihan
            });
        });
    </script>
@endpush