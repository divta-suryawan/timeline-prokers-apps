@extends('layouts.master')
@section('content')
    <div class="card">
       <div class="card-header d-flex justify-content-between">
            <h5>DATA PROGRAM KERJA YANG BELUM BERJALAN</h5>
        </div>
            <div class="table-responsive text-nowrap px-4 py-1">
                <table class="table" id="dataTableProkersPending">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Proker</th>
                        <th>Priode</th>
                        <th>Start</th>
                        <th>Finish</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        @if (auth()->user()->role=='admin')
                        <th>PJ</th>
                        @endif
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    </tbody>
                </table>
            </div>
    </div>

@endsection
@section('scripts')
<script>
    $(document).ready(function(){

            let dataTable = $("#dataTableProkersPending").DataTable({
                "responsive": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 30, 40, 50],
                "autoWidth": false,
            });

            function getDataProkersPending() {
                let status ='pending';
                $.ajax({
                    url: `/v3/prokers/detail/` + status,
                    method: "GET",
                    dataType: "json",
                    success: function (response) {
                        let userRole = response.userRole;
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
                            if (userRole === 'admin') {
                                tableBody += "<td>" + item.users.position + "</td>";
                            }
                            tableBody += "<td >" +
                                "<button type='button' class='btn btn-outline-success btn-sm btn-start'" +
                                "data-id='" + item.id + "'><i class='fa-solid fa-play'></i></button>" +
                                "</td>";
                            tableBody += "</tr>";
                        });
                        let table = $("#dataTableProkersPending").DataTable();
                        table.clear().draw();
                        table.rows.add($(tableBody)).draw();
                    },
                    error: function () {
                        console.log("Failed to get data from server");
                    }
                });
            }
            getDataProkersPending();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(document).on('click', '.btn-start', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin Menjalankan proker ini tolong pastikan tanggalnya kembali',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yakin!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'post',
                            url: `/v3/prokers/start/${id}`,
                            success: function(response) {
                                Swal.fire('Sukses', 'Proker berhasil dijalankan', 'success');
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            },
                            error: function(error) {
                                Swal.fire('Error', 'Gagal  menjalankan proker', 'error');
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
