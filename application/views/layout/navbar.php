<link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>">

<style>
    .sb-topnav {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
        font-weight: 600;
        font-size: 1.1rem;
        letter-spacing: .5px;
    }

    .navbar .form-control {
        border-radius: 20px 0 0 20px;
        font-size: 0.9rem;
    }

    .navbar .btn {
        border-radius: 0 20px 20px 0;
    }

    .navbar-nav .nav-link {
        transition: background-color 0.2s ease-in-out;
        border-radius: 6px;
    }

    .navbar-nav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .dropdown-menu {
        font-size: 0.9rem;
        border-radius: 0.35rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .dropdown-item i {
        width: 18px;
    }
</style>

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark" data-bs-theme="light">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="<?= site_url('/') ?>">
        <i class="fas fa-shipping-fast me-1"></i> Cex
        <?php
        $user = $session['user'];
        echo "($user->role)";
        ?>
    </a>

    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 text-white" id="sidebarToggle" href="#!">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">

    </form>

    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" id="navbarDropdown" href="#" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle fa-lg"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="<?= site_url('profile') ?>"><i class="fas fa-user me-2"></i> Profile</a></li>
                <li><a class="dropdown-item" href="#!"><i class="fas fa-list-alt me-2"></i> Activity Log</a></li>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>