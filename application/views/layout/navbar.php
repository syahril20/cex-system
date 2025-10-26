<!-- Navbar Page -->
<link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>">

<style>
    /* Navbar custom style */
    .sb-topnav {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 1030;
    }

    .navbar-brand {
        font-weight: 600;
        font-size: 1.1rem;
        letter-spacing: .5px;
    }

    .navbar .form-control {
        border-radius: 2rem 0 0 2rem;
        font-size: 0.9rem;
    }

    .navbar .btn {
        border-radius: 0 2rem 2rem 0;
    }

    .navbar-nav .nav-link {
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
        border-radius: 6px;
    }

    .navbar-nav .nav-link:hover {
        background-color: rgba(var(--bs-white-rgb), 0.1);
    }

    .dropdown-menu {
        font-size: 0.9rem;
        border-radius: 0.35rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .dropdown-item i {
        width: 18px;
    }

    /* Responsif: user icon agar rapih di layar kecil */
    @media (max-width: 576px) {
        .navbar .nav-link i {
            font-size: 1.2rem;
        }
    }
</style>

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark" data-bs-theme="dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="<?= site_url('/') ?>">
        <i class="fas fa-shipping-fast me-1"></i> Jijib Express <br>
        <?php if (isset($user->role)): ?>
            <span class="ms-2 text-muted" style="font-size: 0.95em;">(<?= htmlspecialchars($user->role) ?>)</span>
        <?php endif; ?>
    </a>

    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 text-white" id="sidebarToggle" type="button">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Navbar Search (optional) -->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <!-- Future search input here -->
    </form>

    <!-- Navbar User Dropdown -->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" id="navbarDropdown" href="#" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle fa-lg"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="<?= site_url('profile') ?>"><i class="fas fa-user me-2"></i>
                        Profile</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-list-alt me-2"></i> Activity Log</a>
                </li>
                <li>
                    <hr class="dropdown-divider" />
                </li>
                <li>
                    <a class="dropdown-item text-danger" href="<?= site_url('auth/logout') ?>">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>

<!-- Bootstrap JS (bundle) -->
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>