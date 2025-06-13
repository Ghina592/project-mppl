@extends('layouts.main')
@section('title', 'Profile Siswa')

@push('style')
    <style>
        .profile-widget-picture {
            width: 120px;
            height: 120px;
            object-fit: cover; /* Memastikan gambar mengisi area tanpa terdistorsi */
        }
    </style>
@endpush

@section('content')
    <div class="section">
        <div class="section-body">
            <div class="row d-flex justify-content-center">
                <div class="col-12 col-sm-12 col-lg-5">
                    <div class="card profile-widget">
                        <div class="profile-widget-header">
                            {{-- PERBAIKAN UTAMA: Gunakan $siswa->foto langsung karena sudah berupa URL publik --}}
                            @if ($siswa->foto)
                                <img alt="Foto Siswa" src="{{ $siswa->foto }}" class="rounded-circle profile-widget-picture">
                            @else
                                {{-- Placeholder jika tidak ada foto --}}
                                <img alt="Tidak Ada Foto" src="https://via.placeholder.com/120?text=No+Foto" class="rounded-circle profile-widget-picture">
                            @endif
                            <div class="profile-widget-items">
                                <div class="profile-widget-item">
                                    <div class="profile-widget-item-label">NIS</div> {{-- Mengubah NIP menjadi NIS --}}
                                    <div class="profile-widget-item-value">{{ $siswa->nis ?? '-' }}</div> {{-- Tambah null coalescing --}}
                                </div>
                                <div class="profile-widget-item">
                                    <div class="profile-widget-item-label">Telp</div>
                                    <div class="profile-widget-item-value">{{ $siswa->telp ?? '-' }}</div> {{-- Tambah null coalescing --}}
                                </div>
                            </div>
                        </div>
                        <div class="profile-widget-description pb-0">
                            <div class="profile-widget-name">{{ $siswa->nama ?? 'Nama Siswa' }}
                                <div class="text-muted d-inline font-weight-normal">
                                    <div class="slash"></div> siswa {{ $siswa->kelas?->nama_kelas ?? '-' }} {{-- Menggunakan nullsafe --}}
                                </div>
                            </div>
                            <label for="alamat">Alamat</label>
                            <p>{{ $siswa->alamat ?? '-' }}</p> {{-- Tambah null coalescing --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
