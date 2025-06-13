@extends('layouts.main')
@section('title', 'List Mapel')

@push('style')
{{-- Tambahkan link CSS untuk Select2 jika belum ada di layouts.main --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- Jika menggunakan tema Bootstrap 4 untuk Select2 --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
{{-- Jika menggunakan template admin, pastikan style untuk sweetalert (swal) sudah ada --}}
@endpush

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Mata Pelajaran</h4>
                        {{-- Mengubah ID modal menjadi 'addMapelModal' agar lebih deskriptif --}}
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addMapelModal"><i class="fas fa-folder-plus"></i>&nbsp; Tambah Data Mapel</button>
                    </div>
                    <div class="card-body">
                        {{-- Memuat partial alert untuk pesan sukses/gagal non-validasi --}}
                        @include('partials.alert')

                        {{-- Tampilkan error validasi dari controller di luar modal (jika error tidak terkait form di modal) --}}
                        {{-- Logika ini berfungsi jika Anda mengarahkan kembali ke halaman index tanpa menampilkan modal --}}
                        @if ($errors->any() && !old('_token')) {{-- old('_token') bisa jadi indikator bahwa itu post request --}}
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

                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Mapel</th>
                                        <th>Jurusan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Menggunakan @forelse untuk menangani kasus data kosong --}}
                                    @forelse ($mapel as $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $data->nama_mapel }}</td>
                                            {{-- Menggunakan nullsafe operator (?) untuk relasi, pastikan ada relasi 'jurusan' di model Mapel --}}
                                            <td>{{ $data->jurusan->nama_jurusan ?? 'N/A' }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    {{-- Memperbaiki nama route dengan menambahkan 'admin.' prefix --}}
                                                    {{-- **PENTING: HAPUS Crypt::encrypt()** karena controller sekarang menerima ID biasa --}}
                                                    <a href="{{ route('admin.mapel.edit', $data->id) }}" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> &nbsp; Edit</a>

                                                    {{-- Memperbaiki nama route dan menambahkan ml-2 untuk jarak --}}
                                                    {{-- **PENTING: HAPUS Crypt::encrypt()** karena controller sekarang menerima ID biasa --}}
                                                    <form method="POST" action="{{ route('admin.mapel.destroy', $data->id) }}" class="ml-2">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title='Delete'><i class="fas fa-trash-alt"></i> &nbsp; Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data mata pelajaran.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Modal Tambah Data Mata Pelajaran (Menggunakan ID modal yang baru: addMapelModal) --}}
            <div class="modal fade" tabindex="-1" role="dialog" id="addMapelModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Mata Pelajaran</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            {{-- Memperbaiki nama route --}}
                            <form action="{{ route('admin.mapel.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        {{-- Tampilkan error validasi di dalam modal jika ada submit form yang gagal --}}
                                        {{-- old('_token') berfungsi sebagai indikator bahwa request ini berasal dari submit form --}}
                                        @if ($errors->any() && old('_token'))
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
                                        <div class="form-group">
                                            <label for="nama_mapel">Nama Mapel</label>
                                            {{-- Menambahkan kelas is-invalid dan value old() --}}
                                            <input type="text" id="nama_mapel" name="nama_mapel" class="form-control @error('nama_mapel') is-invalid @enderror" placeholder="Nama Mata Pelajaran" value="{{ old('nama_mapel') }}">
                                            @error('nama_mapel')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="jurusan_id">Jurusan</label>
                                            {{-- Menambahkan kelas is-invalid dan value old() --}}
                                            <select id="jurusan_id" name="jurusan_id" class="select2 form-control @error('jurusan_id') is-invalid @enderror">
                                                <option value="">-- Pilih Jurusan --</option>
                                                @foreach ($jurusan as $data_jurusan) {{-- Mengganti $data menjadi $data_jurusan untuk menghindari konflik nama dengan loop mapel --}}
                                                <option value="{{ $data_jurusan->id }}" {{ old('jurusan_id') == $data_jurusan->id ? 'selected' : '' }}>{{ $data_jurusan->nama_jurusan }}</option>
                                                @endforeach
                                            </select>
                                            @error('jurusan_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer br">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
{{-- SweetAlert JS (pastikan ini di-load) --}}
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

{{-- Select2 JS (pastikan ini di-load) --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
    // SweetAlert for Delete Confirmation
    $('.show_confirm').click(function(event) {
        var form = $(this).closest("form");
        event.preventDefault();
        swal({
            title: `Yakin ingin menghapus data ini?`,
            text: "Data akan terhapus secara permanen!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });
    });

    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4', // Pastikan tema ini diatur jika Anda menggunakan select2-bootstrap4-theme
            dropdownParent: $('#addMapelModal') // Penting untuk modal agar dropdown tampil di atas modal
        });

        // Menampilkan modal kembali jika ada error validasi setelah submit form
        @if ($errors->any() && old('_token'))
            $('#addMapelModal').modal('show');
        @endif
    });
</script>
@endpush