@extends('layouts.main')

@section('title', 'Edit Guru')

@push('style')
    {{-- Memuat CSS Select2, jika Anda menggunakannya (misal: select2bs4) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
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
                {{-- Memuat partial alert, pastikan file partials.alert ada --}}
                @include('partials.alert')

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Edit Guru: {{ $guru->nama }}</h4>
                        {{-- Pastikan rute 'admin.guru.index' terdefinisi jika ada di dalam grup admin --}}
                        <a href="{{ route('admin.guru.index') }}" class="btn btn-primary">Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.guru.update', $guru->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Tampilan Foto Guru Saat Ini --}}
                            @if ($guru->foto)
                                <div class="form-group">
                                    <label>Foto Guru Saat Ini</label><br>
                                    <img src="{{ asset('storage/' . $guru->foto) }}" alt="Foto Guru" style="width: 120px; height: auto; object-fit: cover; border-radius: 5px;">
                                </div>
                            @endif

                            {{-- Field Foto Guru --}}
                            <div class="form-group">
                                <label for="foto">Ubah Foto Guru</label>
                                <div class="custom-file">
                                    <input id="foto" type="file" name="foto" class="custom-file-input @error('foto') is-invalid @enderror">
                                    <label class="custom-file-label" for="foto">Pilih file baru (opsional)</label>
                                    @error('foto')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                            </div>

                            {{-- Field Nama Guru --}}
                            <div class="form-group">
                                <label for="nama">Nama Guru</label>
                                <input type="text" id="nama" name="nama"
                                    class="form-control @error('nama') is-invalid @enderror"
                                    placeholder="{{ __('Nama Guru') }}" value="{{ old('nama', $guru->nama) }}">
                                @error('nama')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-flex flex-wrap"> {{-- Menggunakan flex-wrap untuk responsif --}}
                                {{-- Field NIP --}}
                                <div class="form-group flex-fill mr-4"> {{-- flex-fill agar mengisi ruang kosong --}}
                                    <label for="nip">NIP</label>
                                    <input type="text" id="nip" name="nip"
                                        class="form-control @error('nip') is-invalid @enderror"
                                        placeholder="{{ __('NIP Guru') }}" value="{{ old('nip', $guru->nip) }}">
                                    @error('nip')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Field No. Telp --}}
                                <div class="form-group flex-fill">
                                    <label for="no_telp">No. Telp</label>
                                    <input type="text" id="no_telp" name="no_telp"
                                        class="form-control @error('no_telp') is-invalid @enderror"
                                        placeholder="{{ __('No. Telp Guru') }}" value="{{ old('no_telp', $guru->no_telp) }}">
                                    @error('no_telp')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Field Alamat --}}
                            <div class="form-group">
                                <label for="alamat">Alamat</label> {{-- Label yang lebih spesifik --}}
                                <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                    placeholder="{{ __('Alamat Lengkap') }}">{{ old('alamat', $guru->alamat) }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Field Mata Pelajaran --}}
                            <div class="form-group">
                                <label for="mapel_id">Mata Pelajaran</label>
                                {{-- Class 'select2bs4' menunjukkan Anda menggunakan Select2 dengan tema Bootstrap 4 --}}
                                <select id="mapel_id" name="mapel_id" class="select2bs4 form-control @error('mapel_id') is-invalid @enderror">
                                    <option value="">-- Pilih Mapel --</option>
                                    @foreach ($mapel as $data)
                                        <option value="{{ $data->id }}"
                                            {{ (old('mapel_id', $guru->mapel_id) == $data->id) ? 'selected' : '' }}>
                                            {{ $data->nama_mapel }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('mapel_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary"><i class="nav-icon fas fa-save"></i> &nbsp; Simpan Perubahan</button>
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
            $('.select2bs4').select2({
                theme: 'bootstrap4', // Pastikan tema ini diatur jika Anda menggunakan select2-bootstrap4-theme
                placeholder: "-- Pilih Mapel --",
                allowClear: true // Opsi ini memungkinkan pengguna untuk menghapus pilihan
            });

            // Script untuk menampilkan nama file yang dipilih pada input type="file"
            $('#foto').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
            });
        });
    </script>
@endpush