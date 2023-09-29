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
        const urlLogout = 'logout'
        $(document).ready(function() {
            $('#btnLogout').click(function(e) {
                Swal.fire({
                    title: 'Yakin ingin Logout?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    resolveButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        e.preventDefault();
                        $.ajax({
                            url: `{{ url('${urlLogout}') }}`,
                            method: 'POST',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                localStorage.removeItem('access_token');
                                window.location.href = '/login';
                            },
                            error: function(xhr, status, error) {
                                alert('Error: Failed to logout. Please try again.');
                            }
                        });
                    }
                });
            });
        });
    </script>


