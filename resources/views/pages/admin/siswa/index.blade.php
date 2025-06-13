@extends('layouts.main')

@section('title', 'List Siswa')

@push('style')
    {{-- Memuat CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Gaya kustom untuk tata letak tabel agar sesuai dengan gambar */
        #table-2 {
            table-layout: fixed; /* Penting untuk lebar kolom yang tetap */
            width: 100%; /* Pastikan tabel mengisi lebar kontainer */
        }

        #table-2 th,
        #table-2 td {
            padding: 8px 10px; /* Sesuaikan padding agar sesuai dengan jarak di gambar */
            white-space: nowrap; /* Mencegah teks melilit (kecuali kolom Absen) */
            overflow: hidden; /* Sembunyikan konten yang meluap */
            text-overflow: ellipsis; /* Tambahkan elipsis untuk teks yang meluap */
            vertical-align: middle; /* Pusatkan secara vertikal */
        }

        /* Lebar kolom berdasarkan gambar (disesuaikan setelah kolom Absen dihapus) */
        #table-2 th:nth-child(1), /* No */
        #table-2 td:nth-child(1) {
            width: 5%; /* Lebar kolom "No" */
            text-align: center;
        }

        #table-2 th:nth-child(2), /* Nama Siswa */
        #table-2 td:nth-child(2) {
            width: 25%; /* Lebar kolom "Nama Siswa" (ditingkatkan) */
        }

        #table-2 th:nth-child(3), /* NIS */
        #table-2 td:nth-child(3) {
            width: 20%; /* Lebar kolom "NIS" (ditingkatkan) */
        }

        #table-2 th:nth-child(4), /* Kelas */
        #table-2 td:nth-child(4) {
            width: 20%; /* Lebar kolom "Kelas" (ditingkatkan) */
            text-align: center;
        }

        /* Kolom Absen (index ke-5) DIHAPUS dari CSS ini */

        #table-2 th:nth-child(5), /* Aksi (sekarang index ke-5) */
        #table-2 td:nth-child(5) {
            width: 30%; /* Lebar kolom "Aksi" (ditingkatkan) */
            text-align: center;
        }

        /* Gaya untuk tombol "Hapus" */
        #table-2 td .btn {
            padding: 4px 8px; /* Padding lebih kecil untuk tombol */
            font-size: 0.85em; /* Ukuran font lebih kecil untuk teks tombol */
        }

        /* Gaya untuk header tabel (optional, jika tema default tidak sesuai) */
        #table-2 thead th {
            background-color: #f8f9fa; /* Latar belakang abu-abu muda untuk header */
            color: #495057; /* Warna teks lebih gelap */
            border-bottom: 2px solid #dee2e6; /* Pemisah untuk header */
        }

        /*
            CATATAN: CSS di bawah ini untuk menyembunyikan elemen DataTables.
            Jika Anda ingin DataTables berfungsi penuh (search, pagination, dll.),
            hapus kode ini dan pastikan Anda telah memuat aset DataTables Bootstrap 4 yang benar.
            Jika ingin tetap sederhana tanpa fitur DataTables, biarkan ini atau hapus JS DataTables sepenuhnya.
        */
        .dataTables_wrapper .dataTables_paginate,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .fas.fa-chevron-left.fa-10x, /* Ini mungkin tidak perlu jika DataTables sudah disembunyikan */
        .fas.fa-chevron-right.fa-10x, /* Ini mungkin tidak perlu jika DataTables sudah disembunyikan */
        .dataTables_wrapper .paginate_button.previous,
        .dataTables_wrapper .paginate_button.next,
        .dataTables_wrapper .paginate_button {
            display: none !important;
        }

        /* Menyembunyikan scrollbar global (jika ini yang diinginkan) */
        body::-webkit-scrollbar {
            display: none; /* Untuk Chrome, Safari, Opera */
        }
        body {
            -ms-overflow-style: none;    /* Untuk IE dan Edge */
            scrollbar-width: none;   /* Untuk Firefox */
        }
    </style>
@endpush

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Siswa</h4>
                        {{-- Tombol pemicu modal untuk Bootstrap 4 --}}
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addSiswaModal">
                            <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Data Siswa
                        </button>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>NIS</th>
                                        <th>Kelas</th>
                                        {{-- KOLOM 'Absen' DIHAPUS DARI SINI --}}
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($siswa as $data)
                                    {{-- BARIS DEBUGGING: Ini akan menampilkan ID siswa pertama kali loop dieksekusi --}}
                                    {{-- dd($data->id) --}} {{-- Pastikan baris ini dikomentari/dihapus setelah Anda mendapatkan ID --}}
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->nama }}</td>
                                        <td>{{ $data->nis }}</td>
                                        <td>{{ $data->kelas?->nama_kelas }}</td>
                                        {{-- LOGIKA UNTUK KOLOM 'Absen' DIHAPUS DARI SINI --}}
                                        <td>
                                            {{-- Tombol aksi --}}
                                            <div class="d-flex">
                                                {{-- PERBAIKAN: Gunakan Crypt::encrypt() --}}
                                                <a href="{{ route('admin.siswa.show', Crypt::encrypt($data->id)) }}" class="btn btn-primary btn-sm" style="margin-right: 8px"><i class="nav-icon fas fa-user"></i> &nbsp; Profile</a>
                                                {{-- PERBAIKAN: Gunakan Crypt::encrypt() --}}
                                                <a href="{{ route('admin.siswa.edit', Crypt::encrypt($data->id)) }}" class="btn btn-success btn-sm"><i class="nav-icon fas fa-edit"></i> &nbsp; Edit</a>
                                                {{-- PERBAIKAN: Tambahkan admin. prefix dan type="button" --}}
                                                <form method="POST" action="{{ route('admin.siswa.destroy', $data->id) }}" class="d-inline delete-form">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button" class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title='Delete' style="margin-left: 8px"><i class="nav-icon fas fa-trash-alt"></i> &nbsp; Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        {{-- colspan disesuaikan dari 6 menjadi 5 karena satu kolom dihapus --}}
                                        <td colspan="5" class="text-center">Tidak ada data siswa.</td>
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

{{-- Modal Tambah Siswa (Struktur Bootstrap 4 yang Benar) --}}
<div class="modal fade" tabindex="-1" role="dialog" id="addSiswaModal" aria-labelledby="addSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSiswaModalLabel">Tambah Siswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- PERBAIKAN: Tambahkan admin. prefix --}}
            <form action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- Input tersembunyi untuk konteks modal saat validasi --}}
                <input type="hidden" name="modal_context" value="create_siswa">

                <div class="modal-body">
                    {{-- Menampilkan error validasi dari Laravel --}}
                    @if ($errors->any() && old('modal_context') === 'create_siswa')
                    <div class="alert alert-danger alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                            @foreach ($errors->all() as $error )
                                <div>{{ $error }}</div> {{-- Gunakan div agar setiap error di baris baru --}}
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label for="nama">Nama Siswa</label>
                        <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Nama Siswa" value="{{ old('nama') }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <div class="form-group mr-2 flex-grow-1"> {{-- flex-grow-1 agar memenuhi ruang --}}
                            <label for="nis">NIS</label>
                            <input type="number" id="nis" name="nis" class="form-control @error('nis') is-invalid @enderror" placeholder="NIS Siswa" value="{{ old('nis') }}" required>
                            @error('nis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group ml-2 flex-grow-1"> {{-- flex-grow-1 agar memenuhi ruang --}}
                            <label for="telp">No. Telp</label>
                            <input type="number" id="telp" name="telp" class="form-control @error('telp') is-invalid @enderror" placeholder="No. Telp Siswa" value="{{ old('telp') }}">
                            @error('telp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="kelas_id">Kelas</label>
                        <select id="kelas_id" name="kelas_id" class="select2 form-control @error('kelas_id') is-invalid @enderror" required>
                            <option value="">-- Pilih kelas --</option>
                            @foreach ($kelas as $dataKelas )
                            <option value="{{ $dataKelas->id }}" {{ old('kelas_id') == $dataKelas->id ? 'selected' : '' }}>{{ $dataKelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                        @error('kelas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" placeholder="Alamat" rows="3">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="foto">Foto Siswa</label>
                        <div class="custom-file"> {{-- Gunakan custom-file untuk input file Bootstrap 4 --}}
                            <input id="foto" type="file" name="foto" class="custom-file-input @error('foto') is-invalid @enderror">
                            <label class="custom-file-label" for="foto">Pilih file</label>
                        </div>
                        @error('foto')
                            <div class="invalid-feedback d-block">{{ $message }}</div> {{-- d-block agar pesan error tampil --}}
                        @enderror
                    </div>

                </div> {{-- Penutup modal-body --}}

                <div class="modal-footer bg-whitesmoke br"> {{-- modal-footer di dalam form --}}
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div> {{-- Penutup modal-footer --}}
            </form> {{-- Penutup form --}}
        </div> {{-- Penutup modal-content --}}
    </div> {{-- Penutup modal-dialog --}}
</div> {{-- Penutup modal --}}

@endsection

@push('script')
{{-- Memuat JavaScript Select2 (pastikan ini dimuat setelah jQuery) --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // SweetAlert Konfirmasi Hapus
        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name"); // Jika Anda ingin menampilkan nama di SweetAlert, tambahkan data-name ke tombol
            event.preventDefault();
            swal({
                title: `Yakin ingin menghapus data ini?`,
                text: "Data akan terhapus secara permanen!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    form.submit();
                }
            });
        });

        // Inisialisasi Select2 untuk dropdown kelas
        $('#kelas_id').select2({
            placeholder: "-- Pilih kelas --",
            allowClear: true,
            width: '100%',
            // Ini penting agar dropdown Select2 tampil di atas modal
            dropdownParent: $('#addSiswaModal .modal-content')
        });

        // Membuka modal secara otomatis jika ada error validasi
        @if ($errors->any() && old('modal_context') === 'create_siswa')
            $('#addSiswaModal').modal('show');
            // Jika Select2 tidak mengisi nilai lama secara otomatis setelah validasi gagal,
            // tambahkan ini:
            // Periksa jika old('kelas_id') ada dan tidak kosong sebelum memicu perubahan
            @if(old('kelas_id'))
                $('#kelas_id').val("{{ old('kelas_id') }}").trigger('change');
            @endif
        @endif

        // Event listener saat modal ditutup untuk mereset form
        $('#addSiswaModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset();
            // Reset Select2
            $('#kelas_id').val(null).trigger('change');
            // Hapus class is-invalid dan pesan feedback
            $(this).find('.is-invalid').removeClass('is-invalid');
            $(this).find('.invalid-feedback, .text-danger').remove();
            // Reset label input file
            $(this).find('.custom-file-label').html('Pilih file');
        });

        // Event listener untuk custom-file-input agar label menampilkan nama file yang dipilih
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>
@endpush
