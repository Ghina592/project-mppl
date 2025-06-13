@extends('layouts.main')

@section('title', 'Edit Mapel')

@push('style')
    {{-- Memuat CSS Select2 jika Anda menggunakannya (misal: select2bs4) --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Edit Mata Pelajaran: {{ $mapel->nama_mapel }}</h4>
                            <a href="{{ route('admin.mapel.index') }}" class="btn btn-primary">Kembali</a>
                        </div>
                        <div class="card-body">
                            {{-- Memuat partial alert, pastikan file partials.alert ada --}}
                            @include('partials.alert')

                            <form method="POST" action="{{ route('admin.mapel.update', $mapel->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="nama_mapel">Nama Mapel</label>
                                    <input type="text" id="nama_mapel" name="nama_mapel"
                                        class="form-control @error('nama_mapel') is-invalid @enderror"
                                        placeholder="{{ __('Nama Mata Pelajaran') }}"
                                        value="{{ old('nama_mapel', $mapel->nama_mapel) }}">
                                    @error('nama_mapel')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="jurusan_id">Jurusan</label>
                                    <select id="jurusan_id" name="jurusan_id"
                                        class="select2bs4 form-control @error('jurusan_id') is-invalid @enderror">
                                        <option value="">-- Pilih Jurusan --</option>
                                        @foreach ($jurusan as $data_jurusan)
                                            {{-- Logika untuk setting 'selected' --}}
                                            {{-- Prioritaskan old('jurusan_id'), lalu gunakan $mapel->jurusan_id --}}
                                            <option value="{{ $data_jurusan->id }}"
                                                {{ (string)(old('jurusan_id') ?? $mapel->jurusan_id) === (string)$data_jurusan->id ? 'selected' : '' }}>
                                                {{ $data_jurusan->nama_jurusan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jurusan_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
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
            $('.select2bs4').select2({
                theme: 'bootstrap4',
                placeholder: "-- Pilih Jurusan --",
                allowClear: true // Opsi ini memungkinkan pengguna untuk menghapus pilihan
            });
        });
    </script>
@endpush