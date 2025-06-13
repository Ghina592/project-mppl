@extends('layouts.main')
@section('title', 'Edit Tugas') {{-- Memastikan judul menjadi "Edit Tugas" --}}

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @include('partials.alert')
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Edit Tugas {{ $tugas->judul }}</h4>
                            <a href="{{ route('tugas.index') }}" class="btn btn-primary">Kembali</a>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('tugas.update', $tugas->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT') {{-- Pastikan ini ada untuk melakukan update --}}

                                <div class="form-group">
                                    <label for="kelas_id">Kelas</label>
                                    <select id="kelas_id" name="kelas_id" class="select2bs4 form-control @error('kelas_id') is-invalid @enderror">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($kelas as $kls)
                                            <option value="{{ $kls->id }}"
                                                {{-- Mempertahankan pilihan jika ada error validasi atau dari data tugas --}}
                                                @if (old('kelas_id', $tugas->kelas_id) == $kls->id) selected @endif>
                                                {{ $kls->nama_kelas ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kelas_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="judul">Judul</label>
                                    {{-- Menggunakan old() untuk mempertahankan input setelah validasi --}}
                                    <input type="text" id="judul" name="judul" class="form-control @error('judul') is-invalid @enderror" placeholder="{{ __('Judul tugas') }}" value="{{ old('judul', $tugas->judul) }}">
                                    @error('judul')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi</label>
                                    {{-- Menggunakan old() untuk mempertahankan input setelah validasi --}}
                                    <textarea id="deskripsi" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="{{ __('Deskripsi tugas') }}">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                                    @error('deskripsi')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="file">File Tugas</label> {{-- Mengubah 'foto' menjadi 'file' agar konsisten --}}
                                    <div class="input-group">
                                        <div class="custom-file">
                                            {{-- Hapus atribut 'value' pada input type file --}}
                                            {{-- Tambahkan kelas 'custom-file-input' --}}
                                            <input id="file" type="file" name="file" class="custom-file-input @error('file') is-invalid @enderror">
                                            {{-- Tampilkan nama file yang sudah ada di label --}}
                                            <label class="custom-file-label" for="file">{{ $tugas->file ? basename($tugas->file) : 'Pilih file baru (opsional)' }}</label>
                                        </div>
                                    </div>
                                    @error('file')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    {{-- Tampilkan link file yang sudah ada --}}
                                    @if ($tugas->file)
                                        <small class="form-text text-muted">File saat ini: <a href="{{ Storage::url($tugas->file) }}" target="_blank">{{ basename($tugas->file) }}</a></small>
                                    @endif
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
<script type="text/javascript">
    // Script untuk menampilkan nama file yang dipilih pada label input file
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('file');
        if (fileInput) {
            fileInput.addEventListener('change', function (event) {
                const fileName = event.target.files[0] ? event.target.files[0].name : 'Pilih file baru (opsional)';
                const label = fileInput.nextElementSibling; // Get the next sibling (label)
                if (label && label.classList.contains('custom-file-label')) {
                    label.textContent = fileName;
                }
            });
        }
    });
</script>
@endpush
