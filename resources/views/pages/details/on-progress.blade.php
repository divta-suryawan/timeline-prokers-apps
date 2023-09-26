@extends('layouts.master')
@section('content')
    <div class="card">
       <div class="card-header d-flex justify-content-between">
            <h5>DATA PROGRAM KERJA YANG SEDANG BERJALAN</h5>
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

    {{-- modal keterangan --}}
    <div class="modal fade" id="ketModal" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ketModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="ket-form" method="POST">
                        @csrf
                        <div class="row">
                            <input type="hidden" id="id" name="id" value="">
                            <div class="col mb-3">
                                <label for="ket" class="form-label">Keterangan</label>
                                <textarea name="ket" id="ket" cols="30" rows="10" class="form-control">Input here...</textarea>
                                <small id="ket-error" class="text-danger"></small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save-ket">
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
    $(document).ready(function(){
            let dataTable = $("#dataTableProkers").DataTable({
                "responsive": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 30, 40, 50],
                "autoWidth": false,
            });

            function getDataProkersOnProgress() {
                let status ='on-progress';
                $.ajax({
                    url: `/api/v3/prokers/detail/` + status,
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
                                        "<button type='button' class='btn btn-outline-success btn-sm btn-end'" +
                                        "data-id='" + item.id + "'><i class='fa-solid fa-thumbs-up'></i></button>" +
                                        "<button type='button' class='btn btn-outline-danger btn-sm btn-notfinish'" +
                                        "data-id='" + item.id + "'><i class='fa-solid fa-rectangle-xmark'></i></button>" +
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
            getDataProkersOnProgress();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let rute;
            $(document).on('click', '.btn-end', function() {
                let id = $(this).data('id');
                $('#ketModalLabel').text('Keterangan Program Kerja Terlaksana');
                $('#ket-id').val(id);
                $('#ket').val('');
                 $('#ket-error').text('');
                $('#ketModal').modal('show');
                rute = '/api/v3/prokers/finish/' + id;
            });

            $(document).on('click', '.btn-notfinish', function() {
                let id = $(this).data('id');
                $('#ketModalLabel').text('Keterangan Program Kerja Tidak Terlaksana');
                $('#ket-id').val(id);
                $('#ket').val('');
                 $('#ket-error').text('');
                $('#ketModal').modal('show');

                rute = '/api/v3/prokers/notfinish/' + id;
            });

            function showSweetAlert(icon, title, message) {
                Swal.fire({
                    icon: icon,
                    title: title,
                    text: message
                });
            }

            $(document).on('click', '#save-ket', function() {
                var id = $('#ket-id').val();
                var ket = $('#ket').val();
                if (!ket) {
                    $('#ket-error').text('Keterangan harus diisi.');
                    return;
                }
                $('#ket-error').text('');

                var data = {
                    id: id,
                    ket: ket
                };
                $('#btnText').hide();
                $('#btnSpinner').show();

                $.ajax({
                    type: 'POST',
                    url: rute,
                    data: data,
                    success: function(response) {
                        let status = response.status;
                        $('#ketModal').modal('hide');
                        if (status === 'finish') {
                            showSweetAlert('success', 'Success!', 'Selamat Proker anda sudah terlaksana');
                        } else {
                            showSweetAlert('warning', 'Attention!', 'Proker tidak terlaksana  ');
                        }
                        setTimeout(function() {
                                $('#btnText').show();
                                $('#btnSpinner').hide();
                        }, 1500);
                        setTimeout(function() {
                            location.reload();
                        }, 1500);

                    },
                    error: function(error) {
                        showSweetAlert('error', 'Error!', 'Mohon Maaf silahkan periksa kembali inputan anda!');
                        setTimeout(function() {
                                location.reload();
                        }, 1500);
                    }
                });
            });

    })
</script>
@endsection
