@extends('layouts.main')
@section('title', 'Pengaturan')

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Pengaturan</h4>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')

                            {{-- Tampilkan error validasi dari controller --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert">
                                            <span>&times;</span>
                                        </button>
                                        <ul>
                                            @foreach ($errors->all() as $error )
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            {{-- Memperbaiki nama route dengan menambahkan 'admin.' prefix --}}
                            <form method="POST" action="{{ route('admin.pengaturan.update', $pengaturan->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="nama_sekolah">Nama Sekolah</label>
                                    <input type="text" id="nama_sekolah" name="nama_sekolah"
                                        class="form-control @error('nama_sekolah') is-invalid @enderror"
                                        placeholder="Nama Sekolah" value="{{ old('nama_sekolah', $pengaturan->name) }}">
                                    @error('nama_sekolah')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="logo">Logo Sekolah</label>
                                    <div>
                                        {{-- Menggunakan asset() helper untuk URL, dan placeholder jika logo tidak ada --}}
                                        <img src="{{ asset($pengaturan->logo ?? 'https://placehold.co/100x100/E0E0E0/333333?text=No+Logo') }}"
                                            alt="Logo Sekolah" width="100" class="mb-2">
                                    </div>
                                    <input type="file" id="logo" name="logo"
                                        class="form-control @error('logo') is-invalid @enderror">
                                    @error('logo')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="card-footer text-right"> {{-- Menambahkan text-right untuk tombol --}}
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                        &nbsp; Simpan Perubahan</button> {{-- Mengubah nav-icon fas menjadi fas --}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
