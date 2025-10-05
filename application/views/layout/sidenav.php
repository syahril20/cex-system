<style>
    /* Sidebar enhancements */
    .sb-sidenav .sb-sidenav-menu-heading {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        padding: 1rem 1rem 0.5rem;
        color: #adb5bd;
        letter-spacing: .05em;
    }

    .sb-sidenav .nav-link {
        font-size: 0.9rem;
        font-weight: 500;
        padding: 0.75rem 1rem;
        border-radius: 0.35rem;
        margin: 2px 8px;
        transition: all 0.2s ease-in-out;
    }

    .sb-sidenav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateX(4px);
    }

    .sb-sidenav .nav-link.active {
        background-color: #0d6efd;
        color: #fff !important;
        font-weight: 600;
    }

    .sb-sidenav .sb-nav-link-icon {
        font-size: 1rem;
        width: 20px;
        margin-right: 10px;
        text-align: center;
        opacity: 0.85;
    }

    .sb-sidenav-footer {
        background-color: rgba(0, 0, 0, 0.2);
        font-size: 0.85rem;
        padding: 0.75rem 1rem;
        text-align: center;
    }
</style>

<div id="layoutSidenav">

    <!-- Sidebar -->
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">

            <div class="sb-sidenav-menu">
                <div class="nav">

                    <!-- Core -->
                    <div class="sb-sidenav-menu-heading">Core</div>
                    <a class="nav-link <?= ($page == 'Dashboard') ? 'active' : '' ?>" href="<?= site_url('/') ?>">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>

                    <?php
                    $username = $user->username;
                    $code = $user->code;
                    ?>

                    <!-- Super Admin -->
                    <?php if ($code === "SUPER_ADMIN"): ?>
                        <div class="sb-sidenav-menu-heading">Control</div>

                        <a class="nav-link <?= in_array($page, ['UserManagement', 'UserEdit', 'UserCreate']) ? 'active' : '' ?>"
                            href="<?= site_url('/user') ?>">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-user-shield"></i></div>
                            User
                        </a>

                        <a class="nav-link <?= ($page == 'Role') ? 'active' : '' ?>" href="<?= site_url('/role') ?>">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-user-tag"></i></div>
                            Role
                        </a>

                        <a class="nav-link <?= in_array($page, ['Order', 'OrderEdit', 'OrderDetail']) ? 'active' : '' ?>"
                            href="<?= site_url('/order') ?>">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-box-open"></i></div>
                            Order
                        </a>
                    <?php endif; ?>

                    <!-- Admin -->
                    <?php if ($code === 'ADMIN'): ?>
                        <div class="sb-sidenav-menu-heading">Control</div>

                        <a class="nav-link <?= in_array($page, ['UserManagement', 'UserEdit', 'UserCreate']) ? 'active' : '' ?>"
                            href="<?= site_url('/user') ?>">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-user-gear"></i></div>
                            User
                        </a>

                        <a class="nav-link <?= in_array($page, ['Order', 'OrderEdit', 'OrderDetail']) ? 'active' : '' ?>"
                            href="<?= site_url('/order') ?>">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-box-open"></i></div>
                            Order
                        </a>
                    <?php endif; ?>

                    <!-- Agent -->
                    <?php if ($code === 'AGENT'): ?>
                        <div class="sb-sidenav-menu-heading">Order</div>

                        <a class="nav-link <?= in_array($page, ['Order', 'OrderEdit', 'OrderDetail', 'OrderForm', 'UploadForm']) ? 'active' : '' ?>"
                            href="<?= site_url('/order') ?>">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-box-open"></i></div>
                            Order
                        </a>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Footer -->
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                <span class="fw-bold"><?= $username ?></span>
            </div>
        </nav>
    </div>

    <!-- Content Loader -->
    <?php
    if (!empty($page)) {
        switch ($page) {
            case 'Dashboard':
                $this->load->view('dashboard/index');
                break;

            case 'Role':
                if ($code === 'SUPER_ADMIN')
                    $this->load->view('role/index');
                break;

            case 'Order':
                $this->load->view('order/index');
                break;

            case 'OrderForm':
                if ($code === 'AGENT')
                    $this->load->view('order/create');
                break;

            case 'OrderDetail':
                $this->load->view('order/detail');
                break;

            case 'OrderEdit':
                if ($code !== 'AGENT') {
                    $this->load->view('order/edit');
                }
                break;

            case 'UploadForm':
                if ($code === 'AGENT')
                    $this->load->view('content/order/agent_upload_form');
                break;

            case 'UserManagement':
                if ($code === 'SUPER_ADMIN' || $code === 'ADMIN') {
                    $this->load->view('content/user/user_management');
                }
                break;

            case 'UserEdit':
                if ($code === 'SUPER_ADMIN' || $code === 'ADMIN') {
                    $this->load->view('content/user/user_edit');
                }
                break;

            case 'UserCreate':
                if ($code === 'SUPER_ADMIN' || $code === 'ADMIN') {
                    $this->load->view('content/user/user_create');
                }
                break;

            case 'Profile':
                $this->load->view('profile/index');
                break;
        }
    }
    ?>
</div>

<script src="<?= base_url('assets/js/scripts.js') ?>"></script>