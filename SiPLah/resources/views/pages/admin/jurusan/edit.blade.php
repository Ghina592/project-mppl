@extends('layouts.main')

@section('title', 'Edit Jurusan')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                {{-- Memuat partial alert, pastikan file partials.alert ada --}}
                @include('partials.alert')

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Edit Jurusan: {{ $jurusan->nama_jurusan }}</h4>
                        {{-- Sesuaikan rute 'admin.jurusan.index' jika rute jurusan Anda ada di bawah grup 'admin.' --}}
                        <a href="{{ route('admin.jurusan.index') }}" class="btn btn-primary">Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.jurusan.update', $jurusan->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="nama_jurusan">Nama Jurusan</label>
                                <input type="text" id="nama_jurusan" name="nama_jurusan"
                                    class="form-control @error('nama_jurusan') is-invalid @enderror"
                                    placeholder="{{ __('Nama Jurusan') }}"
                                    value="{{ old('nama_jurusan', $jurusan->nama_jurusan) }}"> {{-- Disini perbaikan `old()` --}}
                                @error('nama_jurusan')
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