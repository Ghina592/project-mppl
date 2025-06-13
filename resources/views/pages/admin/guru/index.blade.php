@extends('layouts.main')

@section('title', 'List Guru')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Guru</h4>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addGuruModal"><i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Data Guru</button>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Guru</th>
                                        <th>NIP</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($guru as $data)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->nama }}</td>
                                        <td>{{ $data->nip }}</td>
                                        <td>
                                            {{ $data->mapel?->nama_mapel ?? 'N/A' }}
                                            @if ($data->mapel?->jurusan)
                                                | {{ $data->mapel?->jurusan?->nama_jurusan ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('admin.guru.show', Crypt::encrypt($data->id)) }}" class="btn btn-primary btn-sm" style="margin-right: 8px"><i class="nav-icon fas fa-user"></i> &nbsp; Profile</a>
                                                <a href="{{ route('admin.guru.edit', Crypt::encrypt($data->id)) }}" class="btn btn-success btn-sm"><i class="nav-icon fas fa-edit"></i> &nbsp; Edit</a>
                                                {{-- FORM PENGHAPUSAN --}}
                                                <form method="POST" action="{{ route('admin.guru.destroy', $data->id) }}" class="d-inline delete-form">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button" class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title='Delete' style="margin-left: 8px"><i class="nav-icon fas fa-trash-alt"></i> &nbsp; Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Bagian Modal Tambah Data Guru --}}
            <div class="modal fade" tabindex="-1" role="dialog" id="addGuruModal" aria-labelledby="addGuruModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addGuruModalLabel">Tambah Data Guru</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="modal_context" value="create_guru">
                            <div class="modal-body">
                                @if ($errors->any() && old('modal_context') === 'create_guru')
                                <div class="alert alert-danger alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert">
                                            <span>&times;</span>
                                        </button>
                                        @foreach ($errors->all() as $error )
                                        <div>{{ $error }}</div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                <div class="form-group">
                                    <label for="nama">Nama Guru</label>
                                    <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="{{ __('Nama Guru') }}" value="{{ old('nama') }}" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="form-group mr-2 flex-grow-1">
                                        <label for="nip">NIP</label>
                                        <input type="number" id="nip" name="nip" class="form-control @error('nip') is-invalid @enderror" placeholder="{{ __('NIP Guru') }}" value="{{ old('nip') }}" required>
                                        @error('nip')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group ml-2 flex-grow-1">
                                        <label for="no_telp">No. Telp</label>
                                        <input type="number" id="no_telp" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" placeholder="{{ __('No. Telp Guru') }}" value="{{ old('no_telp') }}">
                                        @error('no_telp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="mapel_id">Mata Pelajaran</label>
                                    <select id="mapel_id" name="mapel_id" class="form-control select2 @error('mapel_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Mapel --</option>
                                        @foreach ($mapel as $dataMapel )
                                        <option value="{{ $dataMapel->id }}" {{ old('mapel_id') == $dataMapel->id ? 'selected' : '' }}>{{ $dataMapel->nama_mapel }} | {{ $dataMapel->jurusan->nama_jurusan ?? 'Tidak Ada Jurusan' }}</option>
                                        @endforeach
                                    </select>
                                    @error('mapel_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" placeholder="{{ __('Alamat') }}" required>{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="foto">Foto Guru</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input id="foto" type="file" name="foto" class="custom-file-input @error('foto') is-invalid @enderror">
                                            <label class="custom-file-label" for="foto">Pilih file</label>
                                        </div>
                                    </div>
                                    @error('foto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
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
</section>
@endsection

@push('script')
{{-- Memuat JavaScript Select2 (pastikan ini dimuat setelah jQuery) --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
{{-- Memuat SweetAlert (pastikan ini dimuat di layout utama atau di sini) --}}
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Event listener untuk tombol hapus
        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            event.preventDefault(); // Mencegah submit form default

            swal({
                title: `Yakin ingin menghapus data ini?`,
                text: "Data akan terhapus secara permanen!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    // Menggunakan AJAX untuk mengirim permintaan DELETE
                    $.ajax({
                        url: form.attr('action'), // Ambil URL dari atribut action form
                        type: 'POST', // Menggunakan POST karena @method('delete') mengubah method ini di Laravel
                        data: form.serialize(), // Mengirim semua data form (termasuk CSRF token)
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.message) {
                                swal('Berhasil!', response.message, 'success')
                                    .then(() => {
                                        // Hapus baris tabel dari DOM setelah sukses
                                        form.closest('tr').remove();
                                    });
                            } else {
                                swal('Berhasil!', 'Data berhasil dihapus!', 'success')
                                    .then(() => {
                                        form.closest('tr').remove();
                                    });
                            }
                        },
                        error: function(xhr) {
                            // Tangani error, misal 409 Conflict dari foreign key constraint
                            let errorMessage = 'Terjadi kesalahan saat menghapus data.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.statusText) {
                                errorMessage = xhr.statusText;
                            }
                            swal('Error!', errorMessage, 'error');
                            console.error('AJAX Error:', xhr);
                        }
                    });
                }
            });
        });

        // Inisialisasi Select2 untuk dropdown mapel
        $('#mapel_id').select2({
            placeholder: "-- Pilih Mapel --",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#addGuruModal .modal-content')
        });

        // Membuka modal secara otomatis jika ada error validasi
        @if ($errors->any() && old('modal_context') === 'create_guru')
            $('#addGuruModal').modal('show');
            @if(old('mapel_id'))
                $('#mapel_id').val("{{ old('mapel_id') }}").trigger('change');
            @endif
        @endif

        // Event listener saat modal ditutup untuk mereset form
        $('#addGuruModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset();
            $('#mapel_id').val(null).trigger('change');
            $(this).find('.is-invalid').removeClass('is-invalid');
            $(this).find('.invalid-feedback, .text-danger').remove();
            $(this).find('.custom-file-label').html('Pilih file');
        });

        // Script untuk menampilkan nama file yang dipilih pada input file custom
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>
@endpush
