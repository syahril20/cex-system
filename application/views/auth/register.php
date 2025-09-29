<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Register</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Create Account</h3>
                                </div>
                                <div class="card-body">
                                    <form action="<?= site_url('auth/do_register') ?>" method="post">

                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputFirstName" type="text" name="username"
                                                placeholder="Enter your first name" required />
                                            <label for="inputFirstName">Username</label>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputEmail" type="email" name="email"
                                                placeholder="name@example.com" required />
                                            <label for="inputEmail">Email address</label>
                                        </div>

                                        <div class="form-floating mb-3 ">
                                            <input class="form-control" id="inputPassword" type="password"
                                                name="password" placeholder="Create a password" required />
                                            <label for="inputPassword">Password</label>
                                        </div>

                                        <div class="form-floating mb-3 mb-md-0">
                                            <select name=" role_id" class="form-select" required>
                                                <?php foreach ($roles as $role): ?>
                                                <option value="<?= $role->id ?>"><?= $role->name ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select><br><br>
                                            <label for="inputRole">Role</label>

                                        </div>

                                        <div class="mt-4 mb-0">
                                            <div class="d-grid">
                                                <button class="btn btn-primary btn-block" type="submit">
                                                    Create Account
                                                </button>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="<?= site_url('login') ?>">Have an account? Go to
                                            login</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Cex 2025</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <?php if ($this->session->flashdata('error')): ?>
            <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="5000">
                <div class="d-flex">
                    <div class="toast-body">
                        <?= $this->session->flashdata('error'); ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'))
            toastElList.forEach(function(toastEl) {
                new bootstrap.Toast(toastEl).show();
            });
        });
        </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="<?= base_url('assets/js/scripts.js') ?>"></script>
</body>

</html>