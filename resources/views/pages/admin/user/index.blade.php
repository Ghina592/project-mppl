@extends('layouts.main') {{-- Menggunakan layout yang Anda sebutkan --}}
@section('title', 'List User')

@push('style')
    {{-- Memuat CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Gaya kustom untuk tata letak tabel agar sesuai dengan gambar */
        #table-2 {
            table-layout: fixed; /* Penting untuk lebar kolom yang tetap */
            width: 100%; /* Pastikan tabel mengisi lebar kontainer */
        }

        #table-2 th,
        #table-2 td {
            padding: 8px 10px; /* Sesuaikan padding agar sesuai dengan jarak di gambar */
            white-space: nowrap; /* Mencegah teks melilit */
            overflow: hidden; /* Sembunyikan konten yang meluap */
            text-overflow: ellipsis; /* Tambahkan elipsis untuk teks yang meluap */
            vertical-align: middle; /* Pusatkan secara vertikal */
        }

        /* Lebar kolom berdasarkan gambar */
        #table-2 th:nth-child(1), /* No */
        #table-2 td:nth-child(1) {
            width: 5%; /* Lebar kolom "No" */
            text-align: center;
        }

        #table-2 th:nth-child(2), /* Nama User */
        #table-2 td:nth-child(2) {
            width: 15%; /* Lebar kolom "Nama User" */
        }

        #table-2 th:nth-child(3), /* Email */
        #table-2 td:nth-child(3) {
            width: 20%; /* Lebar kolom "Email" */
        }

        #table-2 th:nth-child(4), /* Password (Hash) */
        #table-2 td:nth-child(4) {
            width: 35%; /* Lebar kolom "Password (Hash)" */
            font-size: 0.85em; /* Ukuran font sedikit lebih kecil untuk hash */
            /* Properti ini penting untuk memotong teks panjang dan menambahkan elipsis */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #table-2 th:nth-child(5), /* Roles */
        #table-2 td:nth-child(5) {
            width: 10%; /* Lebar kolom "Roles" */
            text-align: center;
        }

        #table-2 th:nth-child(6), /* Aksi */
        #table-2 td:nth-child(6) {
            width: 15%; /* Lebar kolom "Aksi" */
            text-align: center;
        }

        /* Gaya untuk tombol "Hapus" */
        #table-2 td:nth-child(6) .btn {
            padding: 4px 8px; /* Padding lebih kecil untuk tombol */
            font-size: 0.85em; /* Ukuran font lebih kecil untuk teks tombol */
        }

        /*
            PERBAIKAN SEMENTARA UNTUK MENYEMBUNYIKAN PAGINATION DATATABLES YANG TUMPUK
            Ini adalah solusi sementara. Solusi akar adalah menonaktifkan inisialisasi DataTables.
            Gunakan Inspect Element di browser untuk menemukan kelas yang tepat
            dari elemen yang ingin disembunyikan jika kelas di bawah ini tidak bekerja.
        */
        .dataTables_wrapper .dataTables_paginate, /* Sembunyikan pagination DataTables */
        .dataTables_wrapper .dataTables_info,    /* Sembunyikan info "Showing X of Y entries" */
        .dataTables_wrapper .dataTables_length,  /* Sembunyikan dropdown "Show X entries" */
        .dataTables_wrapper .dataTables_filter,  /* Sembunyikan kolom pencarian DataTables */
        /* Sembunyikan panah besar yang mengganggu. Anda perlu Inspect Element
            untuk memastikan kelas atau elemen yang tepat. Ini adalah tebakan umum. */
        .fas.fa-chevron-left.fa-10x, /* Contoh kelas dari Font Awesome yang terlalu besar */
        .fas.fa-chevron-right.fa-10x, /* Contoh kelas dari Font Awesome yang terlalu besar */
        /* Jika elemen panah memiliki struktur yang lebih generik, Anda mungkin perlu lebih spesifik */
        .dataTables_wrapper .paginate_button.previous,
        .dataTables_wrapper .paginate_button.next,
        .dataTables_wrapper .paginate_button {
            display: none !important;
        }

        /* Anda mungkin juga ingin menyembunyikan scrollbar di bagian bawah */
        body::-webkit-scrollbar {
            display: none; /* Untuk Chrome, Safari, Opera */
        }
        body {
            -ms-overflow-style: none;  /* Untuk IE dan Edge */
            scrollbar-width: none;  /* Untuk Firefox */
        }
    </style>
@endpush

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>List User</h4>
                            {{-- Mengubah ID modal menjadi 'addUserModal' agar lebih deskriptif --}}
                            <button class="btn btn-primary" data-toggle="modal" data-target="#addUserModal"><i
                                    class="fas fa-folder-plus"></i>&nbsp; Tambah Data User</button>
                        </div>
                        <div class="card-body">
                            {{-- Menampilkan pesan sukses/error dari session --}}
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
                                            <th>Nama User</th>
                                            <th>Email</th>
                                            <th>Password (Hash)</th>
                                            <th>Roles</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($user as $data) {{-- Menggunakan @forelse untuk handle data kosong --}}
                                            <tr>
                                                {{-- Penomoran halaman yang benar untuk pagination --}}
                                                <td>{{ ($user->currentPage() - 1) * $user->perPage() + $loop->iteration }}</td>
                                                <td>{{ $data->name }}</td>
                                                <td>{{ $data->email }}</td>
                                                <td>{{ $data->password }}</td>
                                                <td>{{ $data->roles }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        {{-- Tombol Edit User --}}
                                                        {{-- Memperbaiki nama route dengan menambahkan 'admin.' prefix --}}
                                                        <a href="{{ route('admin.user.edit', $data->id) }}" class="btn btn-warning btn-sm ml-2"
                                                            data-toggle="tooltip" title='Edit'>
                                                            <i class="fas fa-edit"></i> &nbsp; Edit
                                                        </a>
                                                        {{-- Form Hapus User --}}
                                                        {{-- Memperbaiki nama route dan menambahkan ml-2 untuk jarak --}}
                                                        <form method="POST" action="{{ route('admin.user.destroy', $data->id) }}" class="ml-2">
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
                                                <td colspan="6" class="text-center">Tidak ada data user.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                {{-- Link Pagination --}}
                                <div class="float-right">
                                    {{ $user->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Tambah User (Menggunakan ID modal yang baru: addUserModal) --}}
                <div class="modal fade" role="dialog" id="addUserModal">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Tambah User</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            {{-- Memperbaiki nama route --}}
                            <form action="{{ route('admin.user.store') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    {{-- Hidden input untuk menandai bahwa request berasal dari modal ini --}}
                                    <input type="hidden" name="modal_context" value="create_user">

                                    {{-- Tampilkan error validasi di dalam modal jika ada submit form yang gagal --}}
                                    @if ($errors->any() && old('modal_context') === 'create_user')
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

                                    {{-- Field Nama User --}}
                                    <div class="form-group">
                                        <label for="name">Nama User</label>
                                        <input type="text" id="name" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Nama Lengkap User" value="{{ old('name') }}" required>
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
                                            placeholder="Email User" value="{{ old('email') }}" required>
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
                                            placeholder="Password User" required>
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
                                            class="form-control" placeholder="Ketik Ulang Password" required>
                                    </div>

                                    {{-- Field Roles --}}
                                    <div class="form-group">
                                        <label for="roles">Roles</label>
                                        <select id="roles" name="roles"
                                            class="form-control @error('roles') is-invalid @enderror" required>
                                            <option value="">-- Pilih Roles --</option>
                                            <option value="admin" {{ old('roles') == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="guru" {{ old('roles') == 'guru' ? 'selected' : '' }}>Guru</option>
                                            <option value="siswa" {{ old('roles') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                                            <option value="orangtua" {{ old('roles') == 'orangtua' ? 'selected' : '' }}>Orangtua</option>
                                        </select>
                                        @error('roles')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- Dynamic fields container --}}
                                    <div id="dynamic-fields-container">
                                        {{-- Konten dinamis akan dimuat di sini oleh JavaScript --}}
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
    {{-- Memuat JavaScript Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- Memuat SweetAlert (jika belum dimuat di layouts.main) --}}
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script type="text/javascript">
        // Konfirmasi Hapus Data
        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            event.preventDefault(); // Menggunakan event.preventDefault() untuk mencegah submit default
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

        // Fungsi helper untuk input angka saja
        function inputAngka(event) {
            var charCode = (event.which) ? event.which : event.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }

        $(document).ready(function() {
            // Data yang diteruskan dari Controller ke JavaScript sebagai objek JSON
            const PHP_MAPELS = @json($mapels);
            const PHP_KELAS = @json($kelas);
            const PHP_SISWALIST = @json($siswaList);

            // Fungsi untuk menangani rendering bidang dinamis
            function renderDynamicFields(selectedRole, oldData = {}) {
                var container = $("#dynamic-fields-container");
                container.empty(); // Hapus bidang dinamis sebelumnya

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
                            <h6 class="mt-3">Data Guru</h6>
                            <div class="form-group">
                                <label for="nip">NIP Guru</label>
                                <input id="nip" type="text" onkeypress="return inputAngka(event)" placeholder="NIP Guru"
                                    class="form-control ${errors.nip ? 'is-invalid' : ''}" name="nip"
                                    value="${oldData.nip || ''}" autocomplete="off" required>
                                ${errors.nip ? `<div class="invalid-feedback">${errors.nip[0]}</div>` : ''}
                            </div>

                            <div class="form-group">
                                <label for="mapel_id_guru">Mata Pelajaran Guru</label>
                                <select id="mapel_id_guru" name="mapel_id_guru"
                                    class="form-control select2 ${errors.mapel_id_guru ? 'is-invalid' : ''}" required>
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
                                <textarea id="alamat_guru" placeholder="Alamat Guru"
                                    class="form-control ${errors.alamat_guru ? 'is-invalid' : ''}" name="alamat_guru"
                                    autocomplete="off">${oldData.alamat_guru || ''}</textarea>
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
                            <h6 class="mt-3">Data Siswa</h6>
                            <div class="form-group">
                                <label for="nis">NIS Siswa</label>
                                <input id="nis" type="text" onkeypress="return inputAngka(event)" placeholder="NIS Siswa"
                                    class="form-control ${errors.nis ? 'is-invalid' : ''}" name="nis"
                                    value="${oldData.nis || ''}" autocomplete="off" required>
                                ${errors.nis ? `<div class="invalid-feedback">${errors.nis[0]}</div>` : ''}
                            </div>

                            <div class="form-group">
                                <label for="kelas_id_siswa">Kelas Siswa</label>
                                <select id="kelas_id_siswa" name="kelas_id_siswa"
                                    class="form-control select2 ${errors.kelas_id_siswa ? 'is-invalid' : ''}" required>
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
                                <textarea id="alamat_siswa" placeholder="Alamat Siswa"
                                    class="form-control ${errors.alamat_siswa ? 'is-invalid' : ''}" name="alamat_siswa"
                                    autocomplete="off">${oldData.alamat_siswa || ''}</textarea>
                                ${errors.alamat_siswa ? `<div class="invalid-feedback">${errors.alamat_siswa[0]}</div>` : ''}
                            </div>
                        </div>
                    `;
                } else if (selectedRole === "orangtua") {
                    let siswasOptions = '';
                    const oldSiswasSelected = Array.isArray(oldData.siswas_selected) ? oldData.siswas_selected : [];

                    PHP_SISWALIST.forEach(siswaItem => {
                        if (siswaItem.user) {
                            let selected = oldSiswasSelected.includes(siswaItem.id) ? 'selected' : '';
                            siswasOptions += `<option value="${siswaItem.id}" ${selected}>${siswaItem.user.name} (NIS: ${siswaItem.nis})</option>`;
                        }
                    });

                    htmlContent = `
                        <div>
                            <h6 class="mt-3">Data Orang Tua</h6>
                            <div class="form-group">
                                <label for="no_telp">No Telepon Orang Tua</label>
                                <input id="no_telp" type="text" onkeypress="return inputAngka(event)" placeholder="No Telepon Orang Tua"
                                    class="form-control ${errors.no_telp ? 'is-invalid' : ''}" name="no_telp"
                                    value="${oldData.no_telp || ''}" autocomplete="off" required>
                                ${errors.no_telp ? `<div class="invalid-feedback">${errors.no_telp[0]}</div>` : ''}
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat Orang Tua</label>
                                <textarea id="alamat" placeholder="Alamat Orang Tua"
                                    class="form-control ${errors.alamat ? 'is-invalid' : ''}" name="alamat"
                                    autocomplete="off" required>${oldData.alamat || ''}</textarea>
                                ${errors.alamat ? `<div class="invalid-feedback">${errors.alamat[0]}</div>` : ''}
                            </div>
                            <div class="form-group">
                                <label for="siswas_selected">Daftar Siswa yang Terkait</label>
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

                // Inisialisasi Select2 setelah HTML ditambahkan ke DOM, hanya jika elemennya ada
                // Penting: Pastikan id dari modal yang benar digunakan sebagai dropdownParent
                if (selectedRole === "guru") {
                    $('#mapel_id_guru').select2({
                        dropdownParent: $('#addUserModal .modal-content') // Menggunakan modal-content sebagai parent
                    });
                } else if (selectedRole === "siswa") {
                    $('#kelas_id_siswa').select2({
                        dropdownParent: $('#addUserModal .modal-content') // Menggunakan modal-content sebagai parent
                    });
                } else if (selectedRole === "orangtua") {
                    $('#siswas_selected').select2({
                        placeholder: "Pilih siswa yang terkait",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#addUserModal .modal-content') // Menggunakan modal-content sebagai parent
                    });
                }
            }

            // Membuka modal secara otomatis jika ada error validasi dari attempt submit sebelumnya
            @if ($errors->any() && old('modal_context') === 'create_user')
                $('#addUserModal').modal('show');
                var errorsFromLaravel = {!! json_encode($errors->messages()) !!};
                var oldValues = {
                    name: "{{ old('name') }}",
                    email: "{{ old('email') }}",
                    roles: "{{ old('roles') }}",
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
                    errors: errorsFromLaravel
                };
                renderDynamicFields(oldValues.roles, oldValues);
            @endif


            // Pemicu saat peran berubah di modal
            $('#roles').change(function() {
                var selectedRole = $(this).val();
                renderDynamicFields(selectedRole, {}); // Reset oldData saat ganti role manual
            });

            // Event listener saat modal ditutup
            $('#addUserModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $("#dynamic-fields-container").empty();
                $('.select2').val(null).trigger('change');
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').remove();
                $('#roles').val(''); // Reset dropdown roles
            });

            // Inisialisasi Select2 untuk dropdown roles jika Anda ingin styles Select2 diterapkan padanya.
            // Jika tidak, biarkan default HTML select.
            // $('#roles').select2({
            //     dropdownParent: $('#addUserModal .modal-content')
            // });

        });
    </script>
@endpush
