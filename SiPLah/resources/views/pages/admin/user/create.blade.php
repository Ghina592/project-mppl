@extends('layouts.main')

@section('title', 'Tambah User Baru')

@push('style')
    {{-- Memuat CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Anda bisa menambahkan gaya kustom di sini jika diperlukan */
        .form-group label {
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Tambah User Baru</h4>
                            <div class="card-header-action">
                                <a href="{{ route('user.index') }}" class="btn btn-primary">
                                    <i class="fas fa-list"></i> Kembali ke Daftar User
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Menampilkan pesan sukses/error dari session --}}
                            @include('partials.alert')

                            <form action="{{ route('user.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-12">
                                        {{-- Field Nama User --}}
                                        <div class="form-group">
                                            <label for="name">Nama User</label>
                                            <input type="text" id="name" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                placeholder="{{ __('Nama Lengkap User') }}" value="{{ old('name') }}">
                                            @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Field Email --}}
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" id="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="{{ __('Email User') }}" value="{{ old('email') }}">
                                            @error('email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Field Password --}}
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" id="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="{{ __('Password User') }}">
                                            @error('password')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Field Konfirmasi Password --}}
                                        <div class="form-group">
                                            <label for="password_confirmation">Konfirmasi Password</label>
                                            <input type="password" id="password_confirmation" name="password_confirmation"
                                                class="form-control" placeholder="{{ __('Ketik Ulang Password') }}">
                                        </div>

                                        {{-- Field Roles --}}
                                        <div class="form-group">
                                            <label for="roles">Roles</label>
                                            <select id="roles" name="roles"
                                                class="form-control @error('roles') is-invalid @enderror">
                                                <option value="">-- Pilih Roles --</option>
                                                <option value="admin" {{ old('roles') == 'admin' ? 'selected' : '' }}>
                                                    Admin</option>
                                                <option value="guru" {{ old('roles') == 'guru' ? 'selected' : '' }}>
                                                    Guru</option>
                                                <option value="siswa" {{ old('roles') == 'siswa' ? 'selected' : '' }}>
                                                    Siswa</option>
                                                <option value="orangtua"
                                                    {{ old('roles') == 'orangtua' ? 'selected' : '' }}>Orangtua
                                                </option>
                                            </select>
                                            @error('roles')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Dynamic fields container --}}
                                        <div id="dynamic-fields-container"></div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">Simpan User Baru</button>
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
    {{-- Memuat JavaScript Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- Memuat SweetAlert (opsional, jika diperlukan di halaman ini) --}}
    {{-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> --}}

    <script type="text/javascript">
        $(document).ready(function() {
            // Data yang diteruskan dari Controller ke JavaScript sebagai objek JSON
            // Variabel-variabel ini harus didefinisikan di awal $(document).ready
            // agar bisa diakses oleh renderDynamicFields.
            // Catatan: Di create.blade.php, kita menggunakan $siswas, bukan $siswaList,
            // dan tidak ada $user (karena ini form buat user baru).
            const PHP_MAPELS = @json($mapels);
            const PHP_KELAS = @json($kelas);
            const PHP_SISWAS_FOR_ORANGTUA = @json($siswas); // Gunakan $siswas untuk daftar siswa di form orangtua

            // Fungsi untuk menangani rendering bidang dinamis
            function renderDynamicFields(selectedRole, oldData = {}) {
                var container = $("#dynamic-fields-container");
                container.empty(); // Hapus bidang dinamis sebelumnya

                // Ambil error dari oldData, jika ada
                const errors = oldData.errors || {};

                let htmlContent = '';

                if (selectedRole === "guru") {
                    let mapelsOptions = '<option value="">-- Pilih Mata Pelajaran --</option>';
                    PHP_MAPELS.forEach(mapelItem => {
                        let selected = (oldData.mapel_id_guru == mapelItem.id) ? 'selected' : '';
                        mapelsOptions += `<option value="${mapelItem.id}" ${selected}>${mapelItem.nama_mapel}</option>`;
                    });

                    htmlContent = `
                        <div>
                            <div class="form-group">
                                <label for="nip">NIP Guru</label>
                                <input id="nip" type="text" onkeypress="return inputAngka(event)" placeholder="NIP Guru"
                                    class="form-control ${errors.nip ? 'is-invalid' : ''}" name="nip"
                                    value="${oldData.nip || ''}" autocomplete="off">
                                ${errors.nip ? `<div class="invalid-feedback">${errors.nip[0]}</div>` : ''}
                            </div>

                            <div class="form-group">
                                <label for="mapel_id_guru">Mata Pelajaran Guru</label>
                                <select id="mapel_id_guru" name="mapel_id_guru"
                                    class="form-control select2 ${errors.mapel_id_guru ? 'is-invalid' : ''}">
                                    ${mapelsOptions}
                                </select>
                                ${errors.mapel_id_guru ? `<div class="invalid-feedback">${errors.mapel_id_guru[0]}</div>` : ''}
                            </div>

                            <div class="form-group">
                                <label for="no_telp_guru">No Telepon Guru</label>
                                <input id="no_telp_guru" type="text" onkeypress="return inputAngka(event)" placeholder="No Telepon Guru"
                                    class="form-control ${errors.no_telp_guru ? 'is-invalid' : ''}" name="no_telp_guru"
                                    value="${oldData.no_telp_guru || ''}" autocomplete="off">
                                ${errors.no_telp_guru ? `<div class="invalid-feedback">${errors.no_telp_guru[0]}</div>` : ''}
                            </div>

                            <div class="form-group">
                                <label for="alamat_guru">Alamat Guru</label>
                                <input id="alamat_guru" type="text" placeholder="Alamat Guru"
                                    class="form-control ${errors.alamat_guru ? 'is-invalid' : ''}" name="alamat_guru"
                                    value="${oldData.alamat_guru || ''}" autocomplete="off">
                                ${errors.alamat_guru ? `<div class="invalid-feedback">${errors.alamat_guru[0]}</div>` : ''}
                            </div>
                        </div>
                    `;
                } else if (selectedRole === "siswa") {
                    let kelasOptions = '<option value="">-- Pilih Kelas --</option>';
                    PHP_KELAS.forEach(kelasItem => {
                        let selected = (oldData.kelas_id_siswa == kelasItem.id) ? 'selected' : '';
                        kelasOptions += `<option value="${kelasItem.id}" ${selected}>${kelasItem.nama_kelas}</option>`;
                    });

                    htmlContent = `
                        <div>
                            <div class="form-group">
                                <label for="nis">NIS Siswa</label>
                                <input id="nis" type="text" onkeypress="return inputAngka(event)" placeholder="NIS Siswa"
                                    class="form-control ${errors.nis ? 'is-invalid' : ''}" name="nis"
                                    value="${oldData.nis || ''}" autocomplete="off">
                                ${errors.nis ? `<div class="invalid-feedback">${errors.nis[0]}</div>` : ''}
                            </div>
                            <div class="form-group">
                                <label for="kelas_id_siswa">Kelas Siswa</label>
                                <select id="kelas_id_siswa" name="kelas_id_siswa"
                                    class="form-control select2 ${errors.kelas_id_siswa ? 'is-invalid' : ''}">
                                    ${kelasOptions}
                                </select>
                                ${errors.kelas_id_siswa ? `<div class="invalid-feedback">${errors.kelas_id_siswa[0]}</div>` : ''}
                            </div>
                            <div class="form-group">
                                <label for="telp_siswa">No Telepon Siswa</label>
                                <input id="telp_siswa" type="text" onkeypress="return inputAngka(event)" placeholder="No Telepon Siswa"
                                    class="form-control ${errors.telp_siswa ? 'is-invalid' : ''}" name="telp_siswa"
                                    value="${oldData.telp_siswa || ''}" autocomplete="off">
                                ${errors.telp_siswa ? `<div class="invalid-feedback">${errors.telp_siswa[0]}</div>` : ''}
                            </div>
                            <div class="form-group">
                                <label for="alamat_siswa">Alamat Siswa</label>
                                <input id="alamat_siswa" type="text" placeholder="Alamat Siswa"
                                    class="form-control ${errors.alamat_siswa ? 'is-invalid' : ''}" name="alamat_siswa"
                                    value="${oldData.alamat_siswa || ''}" autocomplete="off">
                                ${errors.alamat_siswa ? `<div class="invalid-feedback">${errors.alamat_siswa[0]}</div>` : ''}
                            </div>
                        </div>
                    `;
                } else if (selectedRole === "orangtua") {
                    let siswasOptions = '';
                    // Gunakan PHP_SISWAS_FOR_ORANGTUA yang sudah di-pass dari controller
                    PHP_SISWAS_FOR_ORANGTUA.forEach(siswaItem => {
                        if (siswaItem.user) { // Pastikan siswaItem.user ada sebelum mengaksesnya
                            let selected = (oldData.siswas_selected && oldData.siswas_selected.includes(siswaItem.id)) ? 'selected' : '';
                            siswasOptions += `<option value="${siswaItem.id}" ${selected}>${siswaItem.user.name}</option>`;
                        }
                    });

                    htmlContent = `
                        <div>
                            <div class="form-group">
                                <label for="no_telp">No Telepon Orang Tua</label>
                                <input id="no_telp" type="text" onkeypress="return inputAngka(event)" placeholder="No Telepon Orang Tua"
                                    class="form-control ${errors.no_telp ? 'is-invalid' : ''}" name="no_telp"
                                    value="${oldData.no_telp || ''}" autocomplete="off">
                                ${errors.no_telp ? `<div class="invalid-feedback">${errors.no_telp[0]}</div>` : ''}
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat Orang Tua</label>
                                <input id="alamat" type="text" placeholder="Alamat Orang Tua"
                                    class="form-control ${errors.alamat ? 'is-invalid' : ''}" name="alamat"
                                    value="${oldData.alamat || ''}" autocomplete="off">
                                ${errors.alamat ? `<div class="invalid-feedback">${errors.alamat[0]}</div>` : ''}
                            </div>
                            <div class="form-group">
                                <label for="siswas_selected">Daftar Siswa</label>
                                <select id="siswas_selected" name="siswas_selected[]"
                                    class="form-control select2 ${errors.siswas_selected ? 'is-invalid' : ''}" multiple="multiple">
                                    ${siswasOptions}
                                </select>
                                ${errors.siswas_selected ? `<div class="invalid-feedback">${errors.siswas_selected[0]}</div>` : ''}
                            </div>
                        </div>
                    `;
                }
                container.html(htmlContent);
                // Inisialisasi Select2 setelah HTML ditambahkan ke DOM
                if (selectedRole === "guru" || selectedRole === "siswa" || selectedRole === "orangtua") {
                    $('.select2').select2();
                }
            }

            // Data yang diteruskan dari Controller ke JavaScript sebagai objek JSON
            // Variabel-variabel ini harus didefinisikan di awal $(document).ready
            // agar bisa diakses oleh renderDynamicFields.
            const PHP_MAPELS = @json($mapels);
            const PHP_KELAS = @json($kelas);
            // Gunakan $siswas untuk create.blade.php
            const PHP_SISWAS_FOR_ORANGTUA = @json($siswas); // Variabel ini sekarang digunakan

            // Logic untuk memuat dynamic fields saat halaman dimuat
            // (karena di create.blade.php tidak ada old('modal_context'))
            var initialRoleOnLoad = $('#roles').val();
            if (initialRoleOnLoad) {
                // Di halaman create, jika ada old input (setelah validasi gagal),
                // maka tampilkan kembali dynamic fields dengan old data.
                var oldValues = {
                    nip: "{{ old('nip') }}",
                    nis: "{{ old('nis') }}",
                    mapel_id_guru: "{{ old('mapel_id_guru') }}",
                    kelas_id_siswa: "{{ old('kelas_id_siswa') }}",
                    no_telp_guru: "{{ old('no_telp_guru') }}",
                    alamat_guru: "{{ old('alamat_guru') }}",
                    telp_siswa: "{{ old('telp_siswa') }}",
                    alamat_siswa: "{{ old('alamat_siswa') }}",
                    no_telp: "{{ old('no_telp') }}",
                    alamat: "{{ old('alamat') }}",
                    siswas_selected: @json(old('siswas_selected', [])),
                    errors: {!! $errors->any() ? $errors->toJson() : '{}' !!}.errors // Ambil objek errors
                };
                renderDynamicFields(initialRoleOnLoad, oldValues);
            }

            // Pemicu saat peran berubah
            $('#roles').change(function() {
                var selectedRole = $(this).val();
                // Ketika peran berubah manual, oldData harus direset
                renderDynamicFields(selectedRole, {});
            });

            // Fungsi helper untuk input angka saja
            function inputAngka(event) {
                var charCode = (event.which) ? event.which : event.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    return false;
                }
                return true;
            }
        });
    </script>
@endpush