@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>DATA USERS</h5>
            <button type="button" class="btn btn-outline-primary" id="add-users" data-bs-toggle="modal" data-bs-target="#usersModal">
                <i class='bx bxs-plus-circle'></i>
            </button>
        </div>
            <div class="table-responsive text-nowrap px-4 py-1">
                <table class="table" id="dataTableUsers">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Email</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">

                    </tbody>
                </table>
            </div>
    </div>
    {{-- modal upsert data --}}
    <div class="modal fade" id="usersModal" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usersModalLabel">Tambah Data</h5>
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
                                <label for="position" class="form-label">Jabatan</label>
                                <input type="text" id="position" name="position" class="form-control" placeholder="Input here..." autocomplete="off">
                                <small id="position-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Input here..." autocomplete="off">
                                <small id="email-error" class="text-danger"></small>
                            </div>
                        </div>
                       <div class="row">
                            <div class="col mb-3">
                                <label for="password" class="form-label" id="passwordModalUsers">Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Input here..." autocomplete="off">
                                    <span class="input-group-text" id="togglePassword">
                                        <i class="far fa-eye"></i>
                                    </span>
                                </div>
                                <small id="password-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="password_confirmation" >Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" >
                                <small id="password_confirmation-error" class="text-danger"></small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveUsers">Save</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            let dataTable = $("#dataTableUsers").DataTable({
                "responsive": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 30, 40, 50],
                "autoWidth": false,
            });

            function getDataUsers() {
                $.ajax({
                    url: `/api/v2/users`,
                    method: "GET",
                    dataType: "json",
                    success: function (response) {
                        let tableBody = "";
                        $.each(response.data, function (index, item) {
                            tableBody += "<tr>";
                            tableBody += "<td>" + (index + 1) + "</td>";
                            tableBody += "<td>" + item.name + "</td>";
                            tableBody += "<td>" + item.position + "</td>";
                            tableBody += "<td>" + item.email + "</td>";
                            tableBody += "<td >" +
                                "<button type='button' class='btn btn-outline-primary edit-modal' data-toggle='modal' " +
                                "data-id='" + item.id + "'>" +
                                "<i class='bx bx-edit-alt'></i></button>" +
                                "<button type='button' class='btn btn-outline-danger delete-confirm' data-id='" +
                                item.id + "'><i class='bx bx-trash' ></i></button>" +
                                "</td>";
                            tableBody += "</tr>";
                        });
                        let table = $("#dataTableUsers").DataTable();
                        table.clear().draw();
                        table.rows.add($(tableBody)).draw();
                    },
                    error: function () {
                        console.log("Failed to get data from server");
                    }
                });
            }
            getDataUsers();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // lihat password
            $(document).ready(function() {
                $("#togglePassword").click(function() {
                    let passwordField = $("#password");
                    let passwordFieldType = passwordField.attr("type");
                    if (passwordFieldType === "password") {
                        passwordField.attr("type", "text");
                        $("#togglePassword i").removeClass("far fa-eye").addClass("far fa-eye-slash");
                    } else {
                        passwordField.attr("type", "password");
                        $("#togglePassword i").removeClass("far fa-eye-slash").addClass("far fa-eye");
                    }
                });
            });

            // get Data by id
            $(document).on('click', '.edit-modal', function() {
                let id = $(this).data('id');
                $('#leadershipModalLabel').text('Edit Data');
                $('#passwordModalUsers').text('New Password');
                $.ajax({
                    type: 'GET',
                    url: `/api/v2/users/get/${id}`,
                    success: function(response) {
                        $('#id').val(response.data.id);
                        $('#name').val(response.data.name);
                        $('#position').val(response.data.position);
                        $('#email').val(response.data.email);
                        $('#password').val(response.data.password);
                        $('#password_confirmation').val(response.data.password_confirmation);
                        $('#usersModal').modal('show');
                    },
                    error: function(error) {
                        console.error('Gagal mengambil data', error);
                    }
                });
            });
            // reset modal
            $(document).on('click', '#add-users', function() {
                $('#usersModalLabel').text('Tambah Data');
                $('#upsertData')[0].reset();
                $('#id').val('');
                $('#usersModal').modal('show');
            });

            // clear alert validasi
            $('#usersModal').on('hidden.bs.modal', function() {
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

            $(document).on('click', '#saveUsers', function(e) {

                $('.text-danger').text('');

                e.preventDefault();
                let id =    $('#id').val();
                let name =        $('#name').val();
                let position =        $('#position').val();
                let email =        $('#email').val();
                let password     =   $('#password').val();
                let password_confirmation    =   $('#password_confirmation').val();

                let data = {
                    name : name,
                    position : position,
                    email : email,
                    password : password,
                    password_confirmation : password_confirmation
                };

                if (id) {
                    $.ajax({
                        type: 'post',
                        url: `/api/v2/users/update/${id}`,
                        data: data,
                        success: function(response) {
                            if (response.code === 422) {
                                let errors = response.errors;
                                $.each(errors, function(key, value) {
                                    $('#' + key + '-error').text(value[0]);
                                });
                            } else if (response.code === 200) {
                                $('#usersModal').modal('hide');
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
                        type: 'POST',
                        url: '/api/v2/users/create',
                        data: data,
                        success: function(response) {
                            if (response.code === 422) {
                                let errors = response.errors;
                                $.each(errors, function(key, value) {
                                    $('#' + key + '-error').text(value[0]);
                                });
                            } else if (response.code === 200) {
                                $('#usersModal').modal('hide');
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
                            url: `/api/v2/users/delete/${id}`,
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
