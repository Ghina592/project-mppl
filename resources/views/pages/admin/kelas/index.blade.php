@extends('layouts.main')
@section('title', 'List Kelas')

@push('style')
{{-- Tambahkan link CSS untuk Select2 jika Anda menggunakannya di form modal --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- Jika menggunakan template admin, pastikan style untuk sweetalert (swal) sudah ada --}}
@endpush

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Kelas</h4>
                        {{-- Mengubah ID modal menjadi 'addClassModal' agar lebih deskriptif --}}
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addClassModal">
                            <i class="fas fa-folder-plus"></i>&nbsp; Tambah Data Kelas
                        </button>
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
                                        <th>Nama Kelas</th>
                                        <th>Jurusan</th>
                                        <th>Wali Kelas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Menggunakan @forelse untuk menangani kasus data kosong --}}
                                    @forelse ($kelas as $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $data->nama_kelas }}</td>
                                            {{-- Menggunakan nullsafe operator (?) untuk relasi --}}
                                            <td>{{ $data->jurusan->nama_jurusan ?? 'N/A' }}</td>
                                            <td>{{ $data->guru->nama ?? 'N/A' }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    {{-- Memperbaiki nama route dengan menambahkan 'admin.' prefix --}}
                                                    <a href="{{ route('admin.kelas.edit', $data->id) }}" class="btn btn-success btn-sm">
                                                        <i class="fas fa-edit"></i> &nbsp; Edit
                                                    </a>
                                                    {{-- Memperbaiki nama route dan menambahkan ml-2 untuk jarak --}}
                                                    <form method="POST" action="{{ route('admin.kelas.destroy', $data->id) }}" class="ml-2">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title='Delete'>
                                                            <i class="fas fa-trash-alt"></i> &nbsp; Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data kelas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Tambah Kelas (Menggunakan ID modal yang baru: addClassModal) -->
            <div class="modal fade" tabindex="-1" role="dialog" id="addClassModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Kelas</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            {{-- Memperbaiki nama route --}}
                            <form action="{{ route('admin.kelas.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        {{-- Tampilkan error validasi di dalam modal jika ada submit form yang gagal --}}
                                        @if ($errors->any() && old('_token'))
                                            <div class="alert alert-danger alert-dismissible show fade">
                                                <div class="alert-body">
                                                    <button class="close" data-dismiss="alert"><span>&times;</span></button>
                                                    <ul>
                                                        @foreach ($errors->all() as $error )
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="form-group">
                                            <label for="nama_kelas">Nama Kelas</label>
                                            {{-- Menambahkan kelas is-invalid dan value old() --}}
                                            <input type="text" id="nama_kelas" name="nama_kelas" class="form-control @error('nama_kelas') is-invalid @enderror" placeholder="Nama Kelas" value="{{ old('nama_kelas') }}">
                                            @error('nama_kelas')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="jurusan_id">Jurusan</label>
                                            {{-- Menambahkan kelas is-invalid dan value old() --}}
                                            <select id="jurusan_id" name="jurusan_id" class="form-control select2 @error('jurusan_id') is-invalid @enderror">
                                                <option value="">-- Pilih Jurusan --</option>
                                                @foreach ($jurusan as $item)
                                                    <option value="{{ $item->id }}" {{ old('jurusan_id') == $item->id ? 'selected' : '' }}>{{ $item->nama_jurusan }}</option>
                                                @endforeach
                                            </select>
                                            @error('jurusan_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="guru_id">Wali Kelas</label>
                                            {{-- Menambahkan kelas is-invalid dan value old() --}}
                                            <select id="guru_id" name="guru_id" class="form-control select2 @error('guru_id') is-invalid @enderror">
                                                <option value="">-- Pilih Wali Kelas --</option>
                                                @foreach ($guru as $data)
                                                    <option value="{{ $data->id }}" {{ old('guru_id') == $data->id ? 'selected' : '' }}>{{ $data->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('guru_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-whitesmoke br">
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
{{-- Select2 JS (pastikan ini di-load jika digunakan) --}}
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
        })
        .then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });
    });

    $(document).ready(function() {
        // Initialize Select2 if it's being used
        // Make sure select2 is only initialized if the elements exist
        if ($('.select2').length) {
            $('.select2').select2({
                dropdownParent: $('#addClassModal') // Penting untuk modal agar dropdown tampil di atas modal
            });
        }

        // Menampilkan modal kembali jika ada error validasi setelah submit form
        @if ($errors->any() && old('_token'))
            $('#addClassModal').modal('show'); // Menggunakan ID modal yang baru
        @endif
    });
</script>
@endpush
