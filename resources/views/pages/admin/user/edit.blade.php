@extends('layouts.main')

@section('title', 'Edit User')

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
                            <h4>Edit User: {{ $user->name }}</h4>
                            <div class="card-header-action">
                                {{-- GANTI BARIS INI: dari user.index menjadi admin.user.index --}}
                                <a href="{{ route('admin.user.index') }}" class="btn btn-primary">
                                    <i class="fas fa-list"></i> Kembali ke Daftar User
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')

                            <form action="{{ route('admin.user.update', $user->id) }}" method="POST"> {{-- JUGA GANTI INI --}}
                                @csrf
                                @method('PUT') {{-- Gunakan metode PUT untuk update --}}

                                <div class="row">
                                    <div class="col-md-12">
                                        {{-- Field Nama User --}}
                                        <div class="form-group">
                                            <label for="name">Nama User</label>
                                            <input type="text" id="name" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                placeholder="{{ __('Nama Lengkap User') }}"
                                                value="{{ old('name', $user->name) }}">
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
                                                placeholder="{{ __('Email User') }}"
                                                value="{{ old('email', $user->email) }}">
                                            @error('email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Field Password (opsional saat edit) --}}
                                        <div class="form-group">
                                            <label for="password">Password (Biarkan kosong jika tidak ingin mengubah)</label>
                                            <input type="password" id="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="{{ __('Password Baru') }}">
                                            @error('password')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Field Konfirmasi Password (opsional saat edit) --}}
                                        <div class="form-group">
                                            <label for="password_confirmation">Konfirmasi Password</label>
                                            <input type="password" id="password_confirmation" name="password_confirmation"
                                                class="form-control" placeholder="{{ __('Ketik Ulang Password Baru') }}">
                                        </div>

                                        {{-- Field Roles --}}
                                        <div class="form-group">
                                            <label for="roles">Roles</label>
                                            <select id="roles" name="roles"
                                                class="form-control @error('roles') is-invalid @enderror">
                                                <option value="">-- Pilih Roles --</option>
                                                <option value="admin" {{ old('roles', $user->roles) == 'admin' ? 'selected' : '' }}>
                                                    Admin</option>
                                                <option value="guru" {{ old('roles', $user->roles) == 'guru' ? 'selected' : '' }}>
                                                    Guru</option>
                                                <option value="siswa" {{ old('roles', $user->roles) == 'siswa' ? 'selected' : '' }}>
                                                    Siswa</option>
                                                <option value="orangtua"
                                                    {{ old('roles', $user->roles) == 'orangtua' ? 'selected' : '' }}>Orangtua
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
                                    <button type="submit" class="btn btn-primary">Update Data User</button>
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

    <script type="text/javascript">
        $(document).ready(function() {
            // Data yang diteruskan dari Controller ke JavaScript sebagai objek JSON
            const PHP_MAPELS = @json($mapels);
            const PHP_KELAS = @json($kelas);
            const PHP_SISWAS_FOR_ORANGTUA = @json($siswasAll);

            // Fungsi untuk menangani rendering bidang dinamis
            function renderDynamicFields(selectedRole, currentData = {}, oldData = {}) {
                var container = $("#dynamic-fields-container");
                container.empty();

                const dataToUse = { ...currentData, ...oldData };
                const errors = oldData.errors || {};

                let htmlContent = '';

                if (selectedRole === "guru") {
                    let mapelsOptions = '<option value="">-- Pilih Mata Pelajaran --</option>';
                    PHP_MAPELS.forEach(mapelItem => {
                        const mapelIdValue = dataToUse.mapel_id_guru || (currentData.mapel ? currentData.mapel.id : null);
                        let selected = (mapelIdValue == mapelItem.id) ? 'selected' : '';
                        mapelsOptions += `<option value="${mapelItem.id}" ${selected}>${mapelItem.nama_mapel}</option>`;
                    });

                    htmlContent = `
                        <div>
                            <div class="form-group">
                                <label for="nip">NIP Guru</label>
                                <input id="nip" type="text" onkeypress="return inputAngka(event)" placeholder="NIP Guru"
                                    class="form-control ${errors.nip ? 'is-invalid' : ''}" name="nip"
                                    value="${dataToUse.nip || ''}" autocomplete="off">
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
                                    value="${dataToUse.no_telp || ''}" autocomplete="off">
                                ${errors.no_telp_guru ? `<div class="invalid-feedback">${errors.no_telp_guru[0]}</div>` : ''}
                            </div>

                            <div class="form-group">
                                <label for="alamat_guru">Alamat Guru</label>
                                <input id="alamat_guru" type="text" placeholder="Alamat Guru"
                                    class="form-control ${errors.alamat_guru ? 'is-invalid' : ''}" name="alamat_guru"
                                    value="${dataToUse.alamat || ''}" autocomplete="off">
                                ${errors.alamat_guru ? `<div class="invalid-feedback">${errors.alamat_guru[0]}</div>` : ''}
                            </div>
                        </div>
                    `;
                } else if (selectedRole === "siswa") {
                    let kelasOptions = '<option value="">-- Pilih Kelas --</option>';
                    PHP_KELAS.forEach(kelasItem => {
                        const kelasIdValue = dataToUse.kelas_id_siswa || (currentData.kelas ? currentData.kelas.id : null);
                        let selected = (kelasIdValue == kelasItem.id) ? 'selected' : '';
                        kelasOptions += `<option value="${kelasItem.id}" ${selected}>${kelasItem.nama_kelas}</option>`;
                    });

                    htmlContent = `
                        <div>
                            <div class="form-group">
                                <label for="nis">NIS Siswa</label>
                                <input id="nis" type="text" onkeypress="return inputAngka(event)" placeholder="NIS Siswa"
                                    class="form-control ${errors.nis ? 'is-invalid' : ''}" name="nis"
                                    value="${dataToUse.nis || ''}" autocomplete="off">
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
                                    value="${dataToUse.telp || ''}" autocomplete="off">
                                ${errors.telp_siswa ? `<div class="invalid-feedback">${errors.telp_siswa[0]}</div>` : ''}
                            </div>
                            <div class="form-group">
                                <label for="alamat_siswa">Alamat Siswa</label>
                                <input id="alamat_siswa" type="text" placeholder="Alamat Siswa"
                                    class="form-control ${errors.alamat_siswa ? 'is-invalid' : ''}" name="alamat_siswa"
                                    value="${dataToUse.alamat || ''}" autocomplete="off">
                                ${errors.alamat_siswa ? `<div class="invalid-feedback">${errors.alamat_siswa[0]}</div>` : ''}
                            </div>
                        </div>
                    `;
                } else if (selectedRole === "orangtua") {
                    let siswasOptions = '';
                    PHP_SISWAS_FOR_ORANGTUA.forEach(siswaItem => {
                        if (siswaItem.user) {
                            const isSelected = (dataToUse.siswas_selected && dataToUse.siswas_selected.includes(siswaItem.id)) ? 'selected' : '';
                            siswasOptions += `<option value="${siswaItem.id}" ${isSelected}>${siswaItem.user.name}</option>`;
                        }
                    });

                    htmlContent = `
                        <div>
                            <div class="form-group">
                                <label for="no_telp">No Telepon Orang Tua</label>
                                <input id="no_telp" type="text" onkeypress="return inputAngka(event)" placeholder="No Telepon Orang Tua"
                                    class="form-control ${errors.no_telp ? 'is-invalid' : ''}" name="no_telp"
                                    value="${dataToUse.no_telp || ''}" autocomplete="off">
                                ${errors.no_telp ? `<div class="invalid-feedback">${errors.no_telp[0]}</div>` : ''}
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat Orang Tua</label>
                                <input id="alamat" type="text" placeholder="Alamat Orang Tua"
                                    class="form-control ${errors.alamat ? 'is-invalid' : ''}" name="alamat"
                                    value="${dataToUse.alamat || ''}" autocomplete="off">
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
                if (selectedRole === "guru" || selectedRole === "siswa" || selectedRole === "orangtua") {
                    $('.select2').select2();
                }
            }

            const currentUser = {
                id: {{ $user->id }},
                roles: "{{ $user->roles }}",
                name: "{{ $user->name }}",
                email: "{{ $user->email }}",
                guru: @json($user->guru),
                siswa: @json($user->siswa),
                orangtua: @json($user->orangtua),
                siswas_selected: @json($orangtua ? $orangtua->siswas->pluck('id')->toArray() : []),
            };

            const oldInputAndErrors = {
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
                errors: {!! $errors->any() ? $errors->toJson() : '{}' !!}.errors
            };

            renderDynamicFields(
                oldInputAndErrors.roles || currentUser.roles,
                currentUser.roles === 'guru' ? currentUser.guru :
                currentUser.roles === 'siswa' ? currentUser.siswa :
                currentUser.roles === 'orangtua' ? currentUser.orangtua : {},
                oldInputAndErrors
            );

            $('#roles').change(function() {
                var selectedRole = $(this).val();
                renderDynamicFields(selectedRole, {});
            });

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