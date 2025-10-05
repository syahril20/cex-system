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
    <title><?php echo $page ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/simple-datatables/style.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>">
    <script src="<?= base_url('assets/js/font-awesome-all.js') ?>"></script>
</head>

<body class="sb-nav-fixed">
    <?php $this->load->view('layout/navbar'); ?>

    <?php
    $this->load->view('layout/sidenav');
    ?>
</body>

</html>