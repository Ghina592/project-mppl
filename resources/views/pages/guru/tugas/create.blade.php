@extends('layouts.main')
@section('title', 'Tambah Tugas')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Tambah Tugas</h4>
                            <a class="btn btn-primary btn-sm" href="{{ route('tugas.index') }}">Kembali</a>
                        </div>
                        <div class="card-body">
                            {{-- Anda mungkin ingin menyertakan partial alert di sini juga --}}
                            @include('partials.alert')

                            <form method="POST" action="{{ route('tugas.store') }}" enctype="multipart/form-data">
                                @csrf
                                {{-- @method('POST') tidak diperlukan di sini karena form defaultnya POST --}}
                                <div class="form-group">
                                    <label for="judul">Judul</label>
                                    <input type="text" id="judul" name="judul" class="form-control @error('judul') is-invalid @enderror" placeholder="{{ __('Judul tugas') }}" value="{{ old('judul') }}" required>
                                    @error('judul')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi</label>
                                    <textarea id="deskripsi" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="{{ __('Deskripsi tugas') }}">{{ old('deskripsi') }}</textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="kelas_id">Kelas</label>
                                    <select id="kelas_id" name="kelas_id" class="select2 form-control @error('kelas_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($kelas as $data )
                                            <option value="{{ $data->id }}" {{ old('kelas_id') == $data->id ? 'selected' : '' }}>{{ $data->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                    @error('kelas_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- MENAMBAHKAN INPUT TANGGAL BATAS DI SINI --}}
                                <div class="form-group">
                                    <label for="tanggal_batas">Tanggal Batas Pengumpulan</label>
                                    <input type="datetime-local" id="tanggal_batas" name="tanggal_batas" class="form-control @error('tanggal_batas') is-invalid @enderror" value="{{ old('tanggal_batas') }}" required>
                                    @error('tanggal_batas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="file">File Tugas (Opsional)</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            {{-- ID input 'file' perlu unik, atau pastikan label 'for' mengacu ke ID yang benar --}}
                                            <input id="file" type="file" name="file" class="custom-file-input @error('file') is-invalid @enderror">
                                            <label class="custom-file-label" for="file">Pilih file</label>
                                        </div>
                                    </div>
                                    @error('file')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 jika Anda menggunakannya
        $('.select2').select2({
            placeholder: "-- Pilih Kelas --",
            allowClear: true,
            width: '100%'
        });

        // Script untuk menampilkan nama file yang dipilih pada input file custom
        $('#file').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });

        // Jika Anda ingin mengatur nilai default tanggal batas ke waktu saat ini
        // let now = new Date();
        // let year = now.getFullYear();
        // let month = (now.getMonth() + 1).toString().padStart(2, '0');
        // let day = now.getDate().toString().padStart(2, '0');
        // let hours = now.getHours().toString().padStart(2, '0');
        // let minutes = now.getMinutes().toString().padStart(2, '0');
        // $('#tanggal_batas').val(`${year}-${month}-${day}T${hours}:${minutes}`);
    });
</script>
@endpush
