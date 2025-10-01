<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="404 Page Not Found" />
        <meta name="author" content="Cex System" />
        <title>404 Error - Cex</title>
        <link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>">
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-light">
        <div id="layoutError">
            <div id="layoutError_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-6">
                                <div class="text-center mt-5">
                                    <img class="mb-4 img-fluid" src="<?= base_url('assets/img/error-404-monochrome.svg') ?>" alt="404 Error" style="max-width:300px;" />
                                    <h1 class="display-4 fw-bold text-danger">404</h1>
                                    <p class="lead text-muted mb-4">The requested URL was not found on this server.</p>
                                    <a href="<?= site_url('/') ?>" class="btn btn-primary">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Return to Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <?php $this->load->view('layout/footer'); ?>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="<?= base_url('assets/js/scripts.js') ?>"></script>
    </body>
</html>
