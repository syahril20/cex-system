<div id="layoutSidenav">

    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Core</div>

                    <a class="nav-link" href="<?= site_url('/') ?>">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        <?php echo $page?>
                    </a>

                    <?php echo "<script>console.log('Debugsda: ', " . json_encode($data) . ");</script>"; ?>

                    <!-- Variables -->
                    <?php
                    $user = $session['user'];
                    $username = $user->username;
                    $code = $user->code;
                    ?>

                    <?php if ($code === "SUPER_ADMIN"): ?>
                        <div class="sb-sidenav-menu-heading">Control</div>
                        <a class="nav-link" href="<?= site_url('/user') ?>">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-users"></i></div>
                            User
                        </a>
                        <a class="nav-link" href="<?= site_url('/role') ?>">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-flag"></i></div>
                            Roles
                        </a>
                        <a class="nav-link" href="<?= site_url('/order') ?>">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                            Order
                        </a>
                    <?php endif; ?>

                    <?php
                    if ($code === 'AGENT'): ?>
                        <a class="nav-link" href="<?= site_url('/order') ?>">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                            Order
                        </a>
                        <a class="nav-link" href="<?= site_url('/order') ?>">
                            <div class="sb-nav-link-icon"><i class="fa-regular fa-images"></i></i></div>
                            Order
                        </a>
                    <?php endif; ?>

                </div>
            </div>

            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                <?php
                echo $username;
                ?>
            </div>

        </nav>
    </div>

    <?php
    if (isset($page) && ($page != null || $page == '')) {
        if ($page == 'Dashboard') {
            if ($code === 'AGENT') {
                $this->load->view('content/dashboard/agent_dashboard');
            }
            // if ($this->session->userdata('user')->code === 'SUPER_ADMIN') {
            //     $this->load->view('content/superadmin_dashboard');
            // }
            // if ($this->session->userdata('user')->code === 'ADMIN') {
            //     $this->load->view('content/admin_dashboard');
            // }
            // if ($this->session->userdata('user')->code === 'AGENT') {
            //     $this->load->view('content/agent_dashboard');
            // }
        }
        if ($page == 'User') {
            $this->load->view('layout/content/user');
        }
        if ($page == 'Order') {
            $this->load->view('layout/content/order');
        }
        if ($page == 'OrderDetail') {
            $this->load->view('layout/content/order_detail');
        }
        if ($page == 'OrderForm') {
            $this->load->view('layout/content/order_form');
        }
        if ($page == 'UploadForm') {
            $this->load->view('layout/content/upload_shipment_form');
        }
    }
    ?>

</div>
<script src="<?= base_url('assets/js/scripts.js') ?>"></script>