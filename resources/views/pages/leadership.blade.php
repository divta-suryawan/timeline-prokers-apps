@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>DATA PERIODE KEPENGURUSAN</h5>
            @if (auth()->user()->role=='admin')
            <button type="button" class="btn btn-outline-primary" id="add-leadership" data-bs-toggle="modal" data-bs-target="#leadershipModal">
                <i class='bx bxs-plus-circle'></i>
            </button>
            @endif
        </div>
            <div class="table-responsive text-nowrap px-4 py-1">
                <table class="table" id="dataTableLeadership">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Periode</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">

                    </tbody>
                </table>
            </div>
    </div>
    {{-- modal upsert data --}}
    <div class="modal fade" id="leadershipModal" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leadershipModalLabel">Tambah Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="upsertData" method="POST">
                        @csrf
                        <div class="row">
                            <input type="hidden" id="id" name="id" value="">
                            <div class="col mb-3">
                                <label for="periode" class="form-label">Periode</label>
                                <input type="text" id="periode" name="periode" class="form-control" placeholder="Input here..." autocomplete="off">
                                <small id="periode-error" class="text-danger"></small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveLeadership">
                        <span id="btnText">Save</span>
                        <span id="btnSpinner" style="display: none;">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        let dataTable = $("#dataTableLeadership").DataTable({
            "responsive": true,
            "lengthChange": true,
            "lengthMenu": [10, 20, 30, 40, 50],
            "autoWidth": false,
        });

       function getDataLeaderShip() {
            $.ajax({
                url: `/v1/leadership`,
                method: "GET",
                dataType: "json",
                success: function (response) {
                    let tableBody = "";
                    let userRole = response.userRole;
                    $.each(response.data, function (index, item) {
                        tableBody += "<tr>";
                        tableBody += "<td>" + (index + 1) + "</td>";
                        tableBody += "<td>" + item.periode + "</td>";
                        tableBody += "<td>";

                        tableBody += "<a href='/prokers/byleadership/" + item.id + "' data-id='" + item.id + "' class='btn btn-outline-warning btn-sm btn-get-byleadership'><i class='fa-regular fa-eye'></i></a>";

                        if (userRole === "admin") {
                            tableBody += "<button type='button' class='btn btn-outline-primary btn-sm edit-modal' data-id='" + item.id + "' data-role='admin'><i class='bx bx-edit-alt'></i></button>";
                            tableBody += "<button type='button' class='btn btn-outline-danger btn-sm delete-confirm' data-id='" + item.id + "' data-role='admin'><i class='bx bx-trash'></i></button>";
                        }

                        tableBody += "</td>";
                        tableBody += "</tr>";
                    });
                    let table = $("#dataTableLeadership").DataTable();
                    table.clear().draw();
                    table.rows.add($(tableBody)).draw();
                },
                error: function () {
                    console.log("Failed to get data from server");
                }
            });
        }

        getDataLeaderShip();



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // get Data by id
        $(document).on('click', '.edit-modal', function() {
            let id = $(this).data('id');
            $('#leadershipModalLabel').text('Edit Data');
            $.ajax({
                type: 'GET',
                url: `/v1/leadership/get/${id}`,
                success: function(response) {
                    $('#id').val(response.data.id);
                    $('#periode').val(response.data.periode);
                    $('#leadershipModal').modal('show');
                },
                error: function(error) {
                    console.error('Gagal mengambil data', error);
                }
            });
        });
        // reset modal
        $(document).on('click', '#add-leadership', function() {
            $('#leadershipModalLabel').text('Tambah Data');
            $('#upsertData')[0].reset();
            $('#id').val('');
            $('#leadershipModal').modal('show');
        });

        // clear alert validasi
        $('#leadershipModal').on('hidden.bs.modal', function() {
            $('.text-danger').text('');
        });

        // alert
        function showSweetAlert(icon, title, message) {
            Swal.fire({
                icon: icon,
                title: title,
                text: message
            });
        }

         $(document).on('click', '#saveLeadership', function(e) {

            $('.text-danger').text('');

            e.preventDefault();
            let id = $('#id').val();
            let periode = $('#periode').val();

            let data = {
                periode: periode,
            };
            $('#btnText').hide();
            $('#btnSpinner').show();
            if (id) {
                $.ajax({
                    type: 'post',
                    url: `/v1/leadership/update/${id}`,
                    data: data,
                    success: function(response) {
                        if (response.code === 422) {
                            let errors = response.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').text(value[0]);
                            });
                        } else if (response.code === 200) {
                            $('#leadershipModal').modal('hide');
                            showSweetAlert('success', 'Success!', 'Data berhasil diperbaharui!');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);

                        }else {
                            showSweetAlert('error', 'Error!', 'Gagal memperbaharui data!');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);

                        }
                        setTimeout(function() {
                                $('#btnText').show();
                                $('#btnSpinner').hide();
                        }, 1500);
                    },
                    error: function(xhr) {
                        console.error('Gagal mengirim permintaan', xhr);
                    }
                });
            } else {
                $.ajax({
                    type: 'POST',
                    url: '/v1/leadership/create',
                    data: data,
                    success: function(response) {
                        if (response.code === 422) {
                            let errors = response.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').text(value[0]);
                            });
                        } else if (response.code === 200) {
                            $('#leadershipModal').modal('hide');
                            showSweetAlert('success', 'Success!', 'Data berhasil ditambahkan!');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }else {
                            $('.loading-container').hide();
                            showSweetAlert('error', 'Error!', 'Gagal menambahkan data!');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }
                        setTimeout(function() {
                                $('#btnText').show();
                                $('#btnSpinner').hide();
                        }, 1500);
                    },
                    error: function(xhr) {
                        console.error('Gagal mengirim permintaan', xhr);
                    }
                });
            }
        });

        // delete data
         $(document).on('click', '.delete-confirm', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Konfirmasi',
                    html: 'Apakah Anda yakin ingin menghapus data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            type: 'DELETE',
                            url: `/v1/leadership/delete/${id}`,
                        });
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (result.value && result.value.code === 200) {
                            Swal.fire('Sukses', 'Data berhasil dihapus', 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            console.log(result);
                            Swal.fire('Error', 'Gagal menghapus data', 'error');
                        }
                    }
                });
        });

    })
</script>
@endsection
