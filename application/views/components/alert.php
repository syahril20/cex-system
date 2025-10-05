<?php if ($this->session->flashdata('swal')): ?>
    <script src="<?= base_url('assets/js/sweetalert2@11.js') ?>"></script>
    <script>
        Swal.fire({
            title: '<?= $this->session->flashdata("swal")["title"] ?>',
            text: '<?= $this->session->flashdata("swal")["text"] ?>',
            icon: '<?= $this->session->flashdata("swal")["icon"] ?>',
            confirmButtonColor: '#3085d6',
            background: '#fff', // warna popup tetap putih
        });
    </script>
    <style>
        /* Paksa backdrop Swal jadi blur */
        .swal2-backdrop-show {
            backdrop-filter: blur(6px) !important;
            -webkit-backdrop-filter: blur(6px) !important;
            background-color: rgba(0, 0, 0, 0.35) !important;
        }
    </style>
<?php endif; ?>