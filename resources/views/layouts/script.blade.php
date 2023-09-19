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
