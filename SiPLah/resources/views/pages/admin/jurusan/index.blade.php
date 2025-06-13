@extends('layouts.main')
@section('title', 'List Jurusan')

@push('style')
{{-- Tambahkan link CSS untuk SweetAlert jika belum ada di layouts.main --}}
{{-- <link href="https://unpkg.com/sweetalert/dist/sweetalert.css" rel="stylesheet"> --}}
@endpush

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Jurusan</h4>
                        {{-- Mengubah ID modal menjadi 'addJurusanModal' agar lebih deskriptif --}}
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addJurusanModal"><i class="fas fa-folder-plus"></i>&nbsp; Tambah Data Jurusan</button>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')

                        {{-- Tampilkan error validasi dari controller di luar modal --}}
                        @if ($errors->any() && !old('_token'))
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
                                        <th>Nama Jurusan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Menggunakan @forelse untuk menangani kasus data kosong --}}
                                    @forelse ($jurusan as $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $data->nama_jurusan }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    {{-- Memperbaiki nama route dengan menambahkan 'admin.' prefix --}}
                                                    <a href="{{ route('admin.jurusan.edit', $data->id) }}" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> &nbsp; Edit</a>
                                                    {{-- Memperbaiki nama route dan menambahkan ml-2 untuk jarak --}}
                                                    <form method="POST" action="{{ route('admin.jurusan.destroy', $data->id) }}" class="ml-2">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title='Delete'><i class="fas fa-trash-alt"></i> &nbsp; Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada data jurusan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Modal Tambah Data Jurusan (Menggunakan ID modal yang baru: addJurusanModal) --}}
            <div class="modal fade" tabindex="-1" role="dialog" id="addJurusanModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Jurusan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            {{-- Memperbaiki nama route --}}
                            <form action="{{ route('admin.jurusan.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        {{-- Tampilkan error validasi di dalam modal jika ada submit form yang gagal --}}
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
                                            <label for="nama_jurusan">Nama Jurusan</label>
                                            {{-- Menambahkan kelas is-invalid dan value old() --}}
                                            <input type="text" id="nama_jurusan" name="nama_jurusan" class="form-control @error('nama_jurusan') is-invalid @enderror" placeholder="Nama Jurusan" value="{{ old('nama_jurusan') }}">
                                            @error('nama_jurusan')
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
        // Menampilkan modal kembali jika ada error validasi setelah submit form
        @if ($errors->any() && old('_token'))
            $('#addJurusanModal').modal('show'); // Menggunakan ID modal yang baru
        @endif
    });
</script>
@endpush
