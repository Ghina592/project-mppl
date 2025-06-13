@extends('layouts.main')
@section('title', 'List Jadwal')

@push('style')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
{{-- Tambahkan link CSS untuk Select2 jika belum ada di layouts.main --}}
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
                        <h4>List Jadwal</h4>
                        {{-- Mengubah ID modal menjadi 'addJadwalModal' agar lebih deskriptif --}}
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addJadwalModal"><i class="fas fa-folder-plus"></i>&nbsp; Tambah Data Jadwal</button>
                    </div>
                    <div class="card-body">
                        {{-- Partial alert dari Bootstrap. Jika ada session 'success' atau 'error' --}}
                        @include('partials.alert')

                        {{-- Tampilkan error validasi dari controller di luar modal (kecuali error jam_tabrakan yang khusus di modal) --}}
                        @if ($errors->any() && !$errors->has('jam_tabrakan') && !old('_token'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                <div class="alert-body">
                                    <button class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                    <ul>
                                        @foreach ($errors->all() as $error)
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
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                        <th>Hari</th>
                                        <th>Dari Jam</th>
                                        <th>Sampai Jam</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Gunakan @forelse untuk handle jika jadwal kosong --}}
                                    @forelse ($jadwal as $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            {{-- Gunakan nullsafe operator (?) untuk relasi --}}
                                            <td>{{ $data->mapel->nama_mapel ?? 'N/A' }}</td>
                                            <td>{{ $data->kelas->nama_kelas ?? 'N/A' }}</td>
                                            {{-- Menggunakan Str::ucfirst untuk kapitalisasi huruf pertama hari --}}
                                            <td>{{ Str::ucfirst($data->hari) }}</td>
                                            {{-- Format jam agar lebih rapi (HH:mm) --}}
                                            <td>{{ \Carbon\Carbon::parse($data->dari_jam)->format('H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($data->sampai_jam)->format('H:i') }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    {{-- Memperbaiki nama route dengan menambahkan 'admin.' prefix --}}
                                                    <a href="{{ route('admin.jadwal.edit', $data->id) }}" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> &nbsp; Edit</a>
                                                    {{-- Menambahkan ml-2 untuk sedikit jarak dan memperbaiki nama route --}}
                                                    <form method="POST" action="{{ route('admin.jadwal.destroy', $data->id) }}" class="ml-2">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title='Delete'><i class="fas fa-trash-alt"></i> &nbsp; Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data jadwal.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Tambah Data Jadwal (Menggunakan ID modal yang baru: addJadwalModal) --}}
            <div class="modal fade" tabindex="-1" role="dialog" id="addJadwalModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Jadwal</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.jadwal.store') }}" method="POST"> {{-- Memperbaiki nama route --}}
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        {{-- Tampilkan error validasi spesifik untuk modal (misalnya jam_tabrakan) --}}
                                        @if ($errors->has('jam_tabrakan') && old('_token'))
                                            <div class="alert alert-danger alert-dismissible show fade">
                                                <div class="alert-body">
                                                    <button class="close" data-dismiss="alert">
                                                        <span>&times;</span>
                                                    </button>
                                                    {{ $errors->first('jam_tabrakan') }}
                                                </div>
                                            </div>
                                        @endif
                                        {{-- Tampilkan error validasi input lainnya di dalam modal (jika modal muncul karena error) --}}
                                        @if ($errors->any() && old('_token') && !$errors->has('jam_tabrakan'))
                                            <div class="alert alert-danger alert-dismissible show fade">
                                                <div class="alert-body">
                                                    <button class="close" data-dismiss="alert">
                                                        <span>&times;</span>
                                                    </button>
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="form-group">
                                            <label for="mapel_id">Mata Pelajaran</label>
                                            <select id="mapel_id" name="mapel_id" class="form-control select2 @error('mapel_id') is-invalid @enderror">
                                                <option value="">-- Pilih Mata Pelajaran --</option>
                                                @foreach ($mapel as $data)
                                                <option value="{{ $data->id }}" {{ old('mapel_id') == $data->id ? 'selected' : '' }}>{{ $data->nama_mapel }}</option>
                                                @endforeach
                                            </select>
                                            @error('mapel_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="kelas_id">Kelas</label>
                                            <select id="kelas_id" name="kelas_id" class="form-control select2 @error('kelas_id') is-invalid @enderror">
                                                <option value="">-- Pilih Kelas --</option>
                                                @foreach ($kelas as $data)
                                                <option value="{{ $data->id }}" {{ old('kelas_id') == $data->id ? 'selected' : '' }}>{{ $data->nama_kelas }}</option>
                                                @endforeach
                                            </select>
                                            @error('kelas_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="hari">Hari</label>
                                            <select id="hari" name="hari" class="form-control select2 @error('hari') is-invalid @enderror">
                                                <option value="">-- Pilih Hari --</option>
                                                @php
                                                    $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
                                                @endphp
                                                @foreach ($days as $day)
                                                <option value="{{ $day }}" {{ old('hari') == $day ? 'selected' : '' }}>{{ ucfirst($day) }}</option>
                                                @endforeach
                                            </select>
                                            @error('hari')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="dari_jam">Mulai dari jam</label>
                                            <input class="form-control @error('dari_jam') is-invalid @enderror" type="text" name="dari_jam" id="time1" value="{{ old('dari_jam') }}"/>
                                            @error('dari_jam')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="sampai_jam">Sampai jam</label>
                                            <input class="form-control @error('sampai_jam') is-invalid @enderror" type="text" name="sampai_jam" id="time2" value="{{ old('sampai_jam') }}"/>
                                            @error('sampai_jam')
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
{{-- SweetAlert --}}
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

{{-- DateTimePicker (Moment.js harus di atasnya) --}}
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

{{-- Select2 JS --}}
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

    // Initialize DateTimePicker
    $(function () {
        $('#time1').datetimepicker({
            format: 'HH:mm',
            stepping: 5,
        });

        $('#time2').datetimepicker({
            format: 'HH:mm',
            stepping: 5,
        });

        // Initialize Select2
        $('.select2').select2({
            dropdownParent: $('#addJadwalModal') // Penting untuk modal agar dropdown tampil di atas modal
        });
    });

    // Menampilkan modal kembali jika ada error validasi setelah submit form
    @if ($errors->any() && old('_token'))
        $(document).ready(function() {
            $('#addJadwalModal').modal('show');
        });
    @endif
</script>

@endpush
