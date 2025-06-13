@extends('layouts.main')
@section('title', 'Edit Siswa')

@push('style')
    {{-- Memuat CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    <style>
        .form-group label {
            font-weight: bold;
        }
        .profile-pic-container {
            width: 120px;
            height: 120px;
            overflow: hidden;
            border-radius: 5px;
            margin-bottom: 15px;
            background-color: #f0f0f0; /* Placeholder background */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .profile-pic-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Edit Siswa: {{ $siswa->nama ?? '-' }}</h4>
                            {{-- PERBAIKAN RUTE: Gunakan 'admin.siswa.index' --}}
                            <a href="{{ route('admin.siswa.index') }}" class="btn btn-primary">Kembali</a>
                        </div>
                        <div class="card-body">
                            {{-- PERBAIKAN RUTE: Gunakan 'admin.siswa.update' --}}
                            <form method="POST" action="{{ route('admin.siswa.update', $siswa->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- Tampilan Foto Siswa Saat Ini --}}
                                <div class="form-group">
                                    <label>Foto Siswa Saat Ini</label>
                                    <div class="profile-pic-container">
                                        @if ($siswa->foto)
                                            <img src="{{ Storage::url($siswa->foto) }}" alt="Foto Siswa">
                                        @else
                                            <img src="https://via.placeholder.com/120?text=No+Foto" alt="Tidak Ada Foto">
                                        @endif
                                    </div>
                                </div>

                                {{-- Input untuk mengubah Foto Siswa --}}
                                <div class="form-group">
                                    <label for="foto">Ubah Foto Siswa</label>
                                    <div class="custom-file">
                                        {{-- ID input file yang benar adalah 'foto' --}}
                                        <input id="foto" type="file" name="foto" class="custom-file-input @error('foto') is-invalid @enderror">
                                        <label class="custom-file-label" for="foto">Pilih file baru (opsional)</label>
                                        @error('foto')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                                </div>

                                <div class="form-group">
                                    <label for="nama">Nama Siswa</label>
                                    <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                        placeholder="{{ __('Nama Siswa') }}" value="{{ old('nama', $siswa->nama) }}">
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex flex-wrap"> {{-- Menggunakan flex-wrap untuk responsif --}}
                                    <div class="form-group mr-2 flex-grow-1">
                                        <label for="nis">NIS</label>
                                        <input type="number" id="nis" name="nis" class="form-control @error('nis') is-invalid @enderror"
                                            placeholder="{{ __('NIS Siswa') }}" value="{{ old('nis', $siswa->nis) }}">
                                        @error('nis')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group ml-2 flex-grow-1">
                                        <label for="telp">No. Telp</label>
                                        <input type="number" id="telp" name="telp" class="form-control @error('telp') is-invalid @enderror"
                                            placeholder="{{ __('No. Telp Siswa') }}" value="{{ old('telp', $siswa->telp) }}">
                                        @error('telp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                        placeholder="{{ __('Alamat') }}" rows="3">{{ old('alamat', $siswa->alamat) }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="kelas_id">Kelas</label>
                                    <select id="kelas_id" name="kelas_id" class="select2bs4 form-control @error('kelas_id') is-invalid @enderror">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($kelas as $data )
                                            <option value="{{ $data->id }}"
                                                {{ (string)(old('kelas_id') ?? $siswa->kelas_id) === (string)$data->id ? 'selected' : '' }}>
                                                {{ $data->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kelas_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary"><i class="nav-icon fas fa-save"></i> &nbsp; Simpan Perubahan</button>
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
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('.select2bs4').select2({
                theme: 'bootstrap4', // Pastikan tema ini diatur jika Anda menggunakan select2-bootstrap4-theme
                placeholder: "-- Pilih Kelas --",
                allowClear: true
            });

            // Event listener untuk custom-file-input agar label menampilkan nama file yang dipilih
            $('#foto').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
            });
        });
    </script>
@endpush