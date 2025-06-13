@extends('layouts.main')
@section('title', 'Edit Materi') {{-- Memastikan judul halaman sesuai --}}

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @include('partials.alert')
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Edit Materi {{ $materi->judul }}</h4>
                            <a href="{{ route('materi.index') }}" class="btn btn-primary">Kembali</a>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('materi.update', $materi->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="file">File Materi</label> {{-- Mengubah 'foto' menjadi 'file' --}}
                                    <div class="input-group">
                                        <div class="custom-file">
                                            {{-- Hapus atribut 'value' pada input type file --}}
                                            {{-- Tambahkan kelas 'custom-file-input' --}}
                                            <input id="file" type="file" name="file" class="custom-file-input @error('file') is-invalid @enderror">
                                            {{-- Tampilkan nama file yang sudah ada di label --}}
                                            <label class="custom-file-label" for="file">{{ $materi->file ? basename($materi->file) : 'Pilih file baru (opsional)' }}</label>
                                        </div>
                                    </div>
                                    @error('file')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    @if ($materi->file)
                                        <small class="form-text text-muted">File saat ini: <a href="{{ Storage::url($materi->file) }}" target="_blank">{{ basename($materi->file) }}</a></small>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="judul">Judul</label>
                                    <input type="text" id="judul" name="judul" class="form-control @error('judul') is-invalid @enderror" placeholder="{{ __('Judul materi') }}" value="{{ old('judul', $materi->judul) }}"> {{-- Menggunakan old() --}}
                                    @error('judul')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi</label> {{-- Menambahkan for="deskripsi" --}}
                                    <textarea id="deskripsi" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="{{ __('Deskripsi materi') }}">{{ old('deskripsi', $materi->deskripsi) }}</textarea> {{-- Menggunakan old() --}}
                                    @error('deskripsi')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="kelas_id">Kelas</label>
                                    <select id="kelas_id" name="kelas_id" class="select2bs4 form-control @error('kelas_id') is-invalid @enderror">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($kelas as $data )
                                            <option value="{{ $data->id }}"
                                                @if (old('kelas_id', $materi->kelas_id) == $data->id) selected @endif> {{-- Menggunakan old() --}}
                                                {{ $data->nama_kelas ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kelas_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
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
