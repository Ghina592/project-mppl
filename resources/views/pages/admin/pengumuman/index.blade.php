@extends('layouts.main')
@section('title', 'List Pengumuman')

@push('style')
    {{-- Memuat CSS SweetAlert (jika belum dimuat di layouts.main) --}}
    {{-- <link href="https://unpkg.com/sweetalert/dist/sweetalert.css" rel="stylesheet"> --}}
@endpush

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            {{-- Mengoreksi judul dari "List Mata Pelajaran" menjadi "List Pengumuman" --}}
                            <h4>List Pengumuman</h4>
                            {{-- Mengubah ID modal menjadi 'addPengumumanModal' agar lebih deskriptif --}}
                            <button class="btn btn-primary" data-toggle="modal" data-target="#addPengumumanModal"><i
                                    class="fas fa-folder-plus"></i>&nbsp; Tambah Data Pengumuman</button>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')

                            {{-- Tampilkan error validasi dari controller di luar modal (jika ada error yang tidak terikat dengan old('_token')) --}}
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
                                            <th>Mulai</th>
                                            <th>Selesai</th>
                                            <th>Pengumuman</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Menggunakan @forelse untuk menangani kasus data kosong --}}
                                        @forelse ($pengumumans as $data)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                {{-- Memformat tanggal agar lebih mudah dibaca --}}
                                                <td>{{ \Carbon\Carbon::parse($data->start_at)->format('d F Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($data->end_at)->format('d F Y') }}</td>
                                                <td>{{ $data->description }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        {{-- Memperbaiki nama route dengan menambahkan 'admin.' prefix --}}
                                                        <a href="{{ route('admin.pengumuman-sekolah.edit', Crypt::encrypt($data->id)) }}"
                                                            class="btn btn-success btn-sm"><i
                                                                class="fas fa-edit"></i> &nbsp; Edit</a>
                                                        {{-- Memperbaiki nama route dan menambahkan ml-2 untuk jarak --}}
                                                        <form method="POST"
                                                            action="{{ route('admin.pengumuman-sekolah.destroy', $data->id) }}" class="ml-2">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="btn btn-danger btn-sm show_confirm"
                                                                data-toggle="tooltip" title='Delete'><i
                                                                    class="fas fa-trash-alt"></i> &nbsp; Hapus</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada data pengumuman.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Modal Tambah Pengumuman (Menggunakan ID modal yang baru: addPengumumanModal) --}}
                <div class="modal fade" tabindex="-1" role="dialog" id="addPengumumanModal">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Tambah Pengumuman</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                {{-- Memperbaiki nama route --}}
                                <form action="{{ route('admin.pengumuman-sekolah.store') }}" method="POST">
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
                                                <label for="start_at">Tanggal Mulai</label>
                                                <input type="date" id="start_at" name="start_at"
                                                    class="form-control @error('start_at') is-invalid @enderror"
                                                    placeholder="Tanggal Mulai" value="{{ old('start_at') }}">
                                                @error('start_at')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="end_at">Tanggal Selesai</label>
                                                <input type="date" id="end_at" name="end_at"
                                                    class="form-control @error('end_at') is-invalid @enderror"
                                                    placeholder="Tanggal Selesai" value="{{ old('end_at') }}">
                                                @error('end_at')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="description">Deskripsi</label>
                                                <textarea id="description" name="description"
                                                    class="form-control @error('description') is-invalid @enderror"
                                                    placeholder="Deskripsi pengumuman">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
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
        // Konfirmasi Hapus Data
        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            // var name = $(this).data("name"); // Variabel 'name' tidak digunakan, bisa dihapus jika tidak relevan
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
            // Menampilkan modal kembali jika ada error validasi setelah submit form
            @if ($errors->any() && old('_token'))
                $('#addPengumumanModal').modal('show'); // Menggunakan ID modal yang baru
            @endif
        });
    </script>
@endpush
