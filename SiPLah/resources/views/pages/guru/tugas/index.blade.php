@extends('layouts.main')

@section('title', 'List Tugas')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Tugas</h4>
                        <a href="{{ route('tugas.create') }}" class="btn btn-primary"><i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Tugas</a>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-tugas">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul Tugas</th>
                                        <th>Deskripsi</th>
                                        <th>Tanggal Batas</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tugas as $data)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->judul }}</td>
                                        <td>{{ $data->deskripsi }}</td>
                                        {{-- PERBAIKAN: Pastikan tanggal_batas adalah objek Carbon sebelum diformat --}}
                                        <td>
                                            @if ($data->tanggal_batas instanceof \Carbon\Carbon)
                                                {{ $data->tanggal_batas->format('d M Y H:i') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $data->mapel->nama_mapel ?? 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('tugas.show', $data->id) }}" class="btn btn-info btn-sm mr-2"><i class="nav-icon fas fa-eye"></i> Lihat</a>
                                                {{-- Menghilangkan tombol Edit --}}
                                                {{-- <a href="{{ route('tugas.edit', $data->id) }}" class="btn btn-success btn-sm mr-2"><i class="nav-icon fas fa-edit"></i> Edit</a> --}}

                                                <form method="POST" action="{{ route('tugas.destroy', $data->id) }}" class="d-inline delete-form">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button" class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title='Hapus'><i class="nav-icon fas fa-trash-alt"></i> Hapus</button>
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
        </div>
    </div>
</section>
@endsection

@push('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#table-tugas').on('click', '.show_confirm', function(event) {
            var form = $(this).closest("form");
            event.preventDefault();

            swal({
                title: `Yakin ingin menghapus tugas ini?`,
                text: "Tugas akan terhapus secara permanen!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.message) {
                                swal('Berhasil!', response.message, 'success')
                                    .then(() => {
                                        form.closest('tr').remove();
                                        updateRowNumbers();
                                    });
                            } else {
                                swal('Berhasil!', 'Data berhasil dihapus!', 'success')
                                    .then(() => {
                                        form.closest('tr').remove();
                                        updateRowNumbers();
                                    });
                            }
                        },
                        error: function(xhr) {
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

        function updateRowNumbers() {
            let i = 1;
            $('#table-tugas tbody tr').each(function() {
                $(this).find('td:first').text(i++);
            });
        }
    });
</script>
@endpush
