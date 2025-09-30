<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<?php echo "<script>console.log('DebugDA: ', " . json_encode($data) . ');</script>';?>
<?php echo "<script>console.log('Debug: ', " . json_encode($data['page']) . ');</script>';?>


<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?php echo $data['page']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <?php $this->load->view('layout/navbar', $data); ?>

    <?php
    $this->load->view('layout/sidenav', $data);
    ?>
</body>

</html>