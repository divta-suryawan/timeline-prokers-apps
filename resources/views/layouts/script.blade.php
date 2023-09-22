<!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('assetAdmin') }}/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="{{ asset('assetAdmin') }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset('assetAdmin') }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset('assetAdmin') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="{{ asset('assetAdmin') }}/assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assetAdmin') }}/assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('assetAdmin') }}/assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="{{ asset('assetAdmin') }}/assets/js/dashboards-analytics.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        const currentUrl = window.location.pathname;
        const menuItems = [
            { id: 'dashboardSidebar', url: '/' },
            { id: 'usersSidebar', url: '/users' },
            { id: 'leadershipSidebar', url: '/leadership' },
            { id: 'prokersSidebar', url: '/prokers' },
            { id: 'pendingStatus', url: '/prokers/pending' },
            { id: 'onProgressStatus', url: '/prokers/onprogress' },
            { id: 'finishStatus', url: '/prokers/finish' },
            { id: 'notFinishStatus', url: '/prokers/notfinish' },
        ];
        menuItems.forEach(item => {
            if (item.url === currentUrl) {
                const menuItem = document.getElementById(item.id);
                if (menuItem) {
                    menuItem.classList.add('active');
                }
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelector('.loading-container').style.display = 'flex';
        });
        window.addEventListener("load", function () {
            setTimeout(function () {
                document.querySelector('.loading-container').style.display = 'none';
            }, 300);
        });
    </script>

    <script>
         $(document).ready(function () {
            function sidebarStatus() {
                $.ajax({
                    url: `/api/v3/prokers/detail/pending`,
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        var statusCounts = response.statusCounts;
                        if (statusCounts.pending > 0) {
                            $("#pendingStatus").show();
                        } else {
                            $("#pendingStatus").hide();
                        }
                        if (statusCounts.on_progress > 0) {
                            $("#onProgressStatus").show();
                        } else {
                            $("#onProgressStatus").hide();
                        }
                        if (statusCounts.finish > 0) {
                            $("#finishStatus").show();
                        } else {
                            $("#finishStatus").hide();
                        }
                        if (statusCounts.not_finish > 0) {
                            $("#notFinishStatus").show();
                        } else {
                            $("#notFinishStatus").hide();
                        }
                    },
                    error: function (error) {
                        console.error('Gagal mendapatkan data statusCounts', error);
                    }
                });
            }
            sidebarStatus();
        });
        </script>


