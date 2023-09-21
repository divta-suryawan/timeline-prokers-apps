@extends('layouts.master')
@section('content')
    <div class="card">
       <div class="card-header d-flex justify-content-between">
            <h5>DATA PROGRAM KERJA</h5>
            <button type="button" class="btn btn-outline-primary" id="add-prokers" data-bs-toggle="modal" data-bs-target="#prokersModal">
                <i class='bx bxs-plus-circle'></i>
            </button>
        </div>
            <div class="table-responsive text-nowrap px-4 py-1">
                <table class="table" id="dataTableProkers">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Proker</th>
                        <th>Priode</th>
                        <th>Start</th>
                        <th>Finish</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Users</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    </tbody>
                </table>
            </div>
    </div>
    {{-- upsert data --}}
     <div class="modal fade" id="prokersModal" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="prokersModalLabel">Tambah Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="upsertData" method="POST">
                        @csrf
                        <input type="hidden" id="id" name="id" value="">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Input here..." autocomplete="off">
                                <small id="name-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="start" class="form-label">Start</label>
                                <input type="date" id="start" name="start" class="form-control" placeholder="Input here..." autocomplete="off">
                                <small id="start-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="end" class="form-label">End</label>
                                <input type="date" id="end" name="end" class="form-control" placeholder="Input here..." autocomplete="off">
                                <small id="end-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="id_leadership" class="form-label">Periode</label>
                                 <select name="id_leadership" id="id_leadership" class="form-control">
                                    <option value="">-- Pilih --</option>
                                </select>
                                <small id="id_leadership-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="id_user" class="form-label">Users</label>
                                <select name="id_user" id="id_user" class="form-control">
                                    <option value="">-- Pilih --</option>
                                </select>
                                <small id="id_users-error" class="text-danger"></small>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveProkers">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    $(document).ready(function(){

            let dataTable = $("#dataTableProkers").DataTable({
                "responsive": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 30, 40, 50],
                "autoWidth": false,
            });

            function getDataProkers() {
                $.ajax({
                    url: `/api/v3/prokers`,
                    method: "GET",
                    dataType: "json",
                    success: function (response) {
                        let tableBody = "";
                        $.each(response.data, function (index, item) {
                            tableBody += "<tr>";
                            tableBody += "<td>" + (index + 1) + "</td>";
                            tableBody += "<td>" + item.name + "</td>";
                            tableBody += "<td>" + item.leadership.periode + "</td>";
                            tableBody += "<td>" + moment(item.start).format('DD MMMM YYYY') + "</td>";
                            tableBody += "<td>" + moment(item.end).format('DD MMMM YYYY') + "</td>";
                            let statusClass = "";
                            let statusText = item.status;
                            let keterangan = item.ket;

                            if (item.status === "pending") {
                                keterangan = "Proker belum berjalan";
                                statusClass = "btn btn-dark btn-sm";
                            } else if (item.status === "on-progress") {
                                keterangan = "Proker sedang berjalan";
                                statusClass = "btn btn-warning btn-sm";
                            }else if (item.status === "finish") {
                                statusClass = "btn btn-success btn-sm";
                            } else if (item.status === "not-finish") {
                                statusClass = "btn btn-danger btn-sm";
                            }
                            if (!keterangan) {
                                keterangan = "Proker belum selesai";
                            }

                            tableBody += "<td><span class='" + statusClass + "'>" + statusText + "</span></td>";
                            tableBody += "<td>" + keterangan + "</td>";
                            tableBody += "<td>" + item.users.name + "</td>";
                            tableBody += "<td >" +
                                "<button type='button' class='btn btn-outline-primary edit-modal' data-toggle='modal' " +
                                "data-id='" + item.id + "'>" +
                                "<i class='bx bx-edit-alt'></i></button>" +
                                "<button type='button' class='btn btn-outline-danger delete-confirm' data-id='" +
                                item.id + "'><i class='bx bx-trash' ></i></button>" +
                                "</td>";
                            tableBody += "</tr>";
                        });
                        let table = $("#dataTableProkers").DataTable();
                        table.clear().draw();
                        table.rows.add($(tableBody)).draw();
                    },
                    error: function () {
                        console.log("Failed to get data from server");
                    }
                });
            }
            getDataProkers();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function getleaderhipNew() {
                $.ajax({
                    url: '/api/v1/leadership',
                    method: 'GET',
                    dataType: 'json',
                     success: function (response){
                        $.each(response.data, function (index, item) {
                            $('#id_leadership').append('<option value="' + item.id + '">' + item.periode + '</option>');
                        });
                    },
                    error: function () {
                        console.log('Failed to get user data from server');
                    }
                });
            }
            // get user
            function getDataUser() {
                $.ajax({
                    url: '/api/v2/users',
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        $.each(response.data, function (index, user) {
                            $('#id_user').append('<option value="' + user.id + '">' + user.name + '</option>');
                        });
                    },
                    error: function () {
                        console.log('Failed to get user data from server');
                    }
                });
            }



            $('#prokersModal').on('shown.bs.modal', function () {
                getleaderhipNew();
                getDataUser();
                $('.text-danger').text('');
            });

            // edit
            $(document).on('click', '.edit-modal', function() {
                let id = $(this).data('id');
                $('#prokersModalLabel').text('Edit Data');
                $.ajax({
                    type: 'GET',
                    url: `/api/v3/prokers/get/${id}`,
                    success: function(response) {
                        $('#id').val(response.data.id);
                        $('#name').val(response.data.name);
                        $('#start').val(response.data.start);
                        $('#end').val(response.data.end);
                        $('#id_user').val(response.data.id_user);

                        $('#prokersModal').modal('show');
                    },
                    error: function(error) {
                        console.error('Gagal mengambil data', error);
                    }
                });
            });
            // reset modal
            $(document).on('click', '#add-prokers', function() {
                $('#prokerspModalLabel').text('Tambah Data');
                $('#upsertData')[0].reset();
                $('#id').val('');
                $('#prokerspModal').modal('show');
            });


            // alert
            function showSweetAlert(icon, title, message) {
                Swal.fire({
                    icon: icon,
                    title: title,
                    text: message
                });
            }

            $(document).on('click', '#saveProkers', function(e) {

                $('.text-danger').text('');

                e.preventDefault();
                let id = $('#id').val();
                let name= $('#name').val();
                let start= $('#start').val();
                let end= $('#end').val();
                let id_leadership= $('#id_leadership').val();
                let id_user= $('#id_user').val();

                let data = {
                    name  : name,
                    start : start,
                    end : end,
                    id_leadership : id_leadership,
                    id_user : id_user,
                };

                if (id) {
                    $.ajax({
                        type: 'post',
                        url: `/api/v3/prokers/update/${id}`,
                        data: data,
                        success: function(response) {
                            if (response.code === 422) {
                                let errors = response.errors;
                                $.each(errors, function(key, value) {
                                    $('#' + key + '-error').text(value[0]);
                                });
                            } else if (response.code === 200) {
                                $('#prokersModal').modal('hide');
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
                        },
                        error: function(xhr) {
                            console.error('Gagal mengirim permintaan', xhr);
                        }
                    });
                } else {
                    $.ajax({
                        type: 'post',
                        url: '/api/v3/prokers/create',
                        data: data,
                        success: function(response) {

                            if (response.code === 422) {
                                let errors = response.errors;
                                $.each(errors, function(key, value) {
                                    $('#' + key + '-error').text(value[0]);
                                });
                            } else if (response.code === 200) {
                                $('#prokersModal').modal('hide');
                                showSweetAlert('success', 'Success!', 'Data berhasil ditambahkan!');
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            }else {
                                showSweetAlert('error', 'Error!', 'Gagal menambahkan data!');
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            }
                        },
                        error: function(xhr) {
                            console.error('Gagal mengirim permintaan', xhr);
                        }
                    });
                }
            });

            $(document).on('click', '.delete-confirm', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menghapus data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'DELETE',
                            url: `/api/v3/prokers/delete/${id}`,
                            success: function(response) {
                                Swal.fire('Sukses', 'Data berhasil dihapus', 'success');
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            },
                            error: function(error) {
                                Swal.fire('Error', 'Gagal menghapus data', 'error');
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            }
                        });
                    }
                });
            });
    })
</script>
@endsection
