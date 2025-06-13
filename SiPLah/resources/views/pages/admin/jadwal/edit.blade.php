@extends('layouts.main')
@section('title', 'Edit Jadwal')

@push('style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .form-group label {
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                @include('partials.alert')
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Edit jadwal mapel {{ $jadwal->mapel->nama_mapel }} pada hari {{ $jadwal->hari }}</h4>
                        <a href="{{ route('admin.jadwal.index') }}" class="btn btn-primary">Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.jadwal.update', $jadwal->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="mapel_id">Mata Pelajaran</label>
                                <select id="mapel_id" name="mapel_id" class="form-control select2">
                                    @foreach ($mapel as $data)
                                        <option value="{{ $data->id }}" {{ (string)(old('mapel_id') ?? $jadwal->mapel_id) === (string)$data->id ? 'selected' : '' }}>
                                            {{ $data->nama_mapel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="kelas_id">Kelas</label>
                                <select id="kelas_id" name="kelas_id" class="form-control select2">
                                    @foreach ($kelas as $data)
                                        <option value="{{ $data->id }}" {{ (string)(old('kelas_id') ?? $jadwal->kelas_id) === (string)$data->id ? 'selected' : '' }}>
                                            {{ $data->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="hari">Hari</label>
                                <select id="hari" name="hari" class="form-control select2">
                                    @foreach($hari as $item)
                                        <option value="{{ $item }}" {{ old('hari', $jadwal->hari) == $item ? 'selected' : '' }}>{{ Str::ucfirst($item) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="dari_jam">Mulai dari jam</label>
                                <input class="form-control timepicker @error('dari_jam') is-invalid @enderror" type="text" name="dari_jam" id="dari_jam" placeholder="{{ __('Jam mulai pelajaran') }}" value="{{ old('dari_jam', $jadwal->dari_jam) }}" />
                                @error('dari_jam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="sampai_jam">Sampai dari jam</label>
                                <input class="form-control timepicker @error('sampai_jam') is-invalid @enderror" type="text" name="sampai_jam" id="sampai_jam" placeholder="{{ __('Jam selesai pelajaran') }}" value="{{ old('sampai_jam', $jadwal->sampai_jam) }}" />
                                @error('sampai_jam')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script> {{-- moment.js harus sebelum datetimepicker --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DateTimePicker
            $('.timepicker').datetimepicker({
                format: 'HH:mm', // <-- UBAH DARI 'HH:mm:ss' MENJADI 'HH:mm'
                icons: {
                    time: 'far fa-clock'
                },
                // Tambahkan pengaturan locale jika perlu, contoh untuk bahasa Indonesia
                // locale: 'id'
            });

            // Inisialisasi Select2
            $('.select2').select2({
                placeholder: "-- Pilih Data --",
                allowClear: true
            });
        });
    </script>
@endpush