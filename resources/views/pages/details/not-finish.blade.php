@extends('layouts.master')
@section('content')
    <div class="card">
       <div class="card-header d-flex justify-content-between">
            <h5>DATA PROGRAM KERJA TIDAK TERLAKSANA</h5>
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
                        @if (auth()->user()->role=='admin')
                        <th>PJ</th>
                        @endif
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
            let dataTable = $("#dataTableProkers").DataTable({
                "responsive": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 30, 40, 50],
                "autoWidth": false,
            });

            function getDataProkersNotFinish() {
                let status ='not-finish';
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
            getDataProkersNotFinish();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

    })
</script>
@endsection
