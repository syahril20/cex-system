<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Login</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <!-- Header (putih biar kontras) -->
                                <div class="card-header bg-white text-primary text-center">
                                    <h3 class="my-2 fw-semibold">
                                        <i class="fas fa-sign-in-alt me-2"></i> Login
                                    </h3>
                                </div>

                                <!-- Body -->
                                <div class="card-body">
                                    <form action="<?= site_url('auth/do_login') ?>" method="post">
                                        <!-- Email -->
                                        <div class="form-floating mb-3">
                                            <input class="form-control shadow-sm" id="inputEmail" type="email"
                                                placeholder="name@example.com" name="email" required />
                                            <label for="inputEmail"><i class="fas fa-envelope me-1"></i> Email</label>
                                        </div>

                                        <!-- Password -->
                                        <div class="form-floating mb-3">
                                            <input class="form-control shadow-sm" id="inputPassword" type="password"
                                                placeholder="Password" name="password" required />
                                            <label for="inputPassword"><i class="fas fa-lock me-1"></i> Password</label>
                                        </div>

                                        <!-- Remember -->
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" id="inputRememberPassword" type="checkbox"
                                                value="1" />
                                            <label class="form-check-label" for="inputRememberPassword">Ingat
                                                Saya</label>
                                        </div>

                                        <!-- Button -->
                                        <div class="d-grid">
                                            <button class="btn btn-primary fw-semibold" type="submit">
                                                <i class="fas fa-sign-in-alt me-1"></i> Login
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Footer -->
                                <div class="card-footer text-center py-3 bg-light">
                                    <div class="small">
                                        <a href="<?= site_url('register') ?>" class="fw-semibold text-decoration-none">
                                            <i class="fas fa-user-plus me-1"></i> Buat Akun Baru
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <?php $this->load->view('layout/footer') ?>

        <!-- SweetAlert Flash -->
        <?php if ($this->session->flashdata('swal')): ?>
            <script>
                Swal.fire({
                    title: '<?= $this->session->flashdata("swal")["title"] ?>',
                    text: '<?= $this->session->flashdata("swal")["text"] ?>',
                    icon: '<?= $this->session->flashdata("swal")["icon"] ?>'
                });
            </script>
        <?php endif; ?>
    </div>
</body>

</html>