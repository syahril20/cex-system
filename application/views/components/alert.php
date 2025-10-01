<?php if ($this->session->flashdata('swal')): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            title: '<?= $this->session->flashdata("swal")["title"] ?>',
            text: '<?= $this->session->flashdata("swal")["text"] ?>',
            icon: '<?= $this->session->flashdata("swal")["icon"] ?>',
            confirmButtonColor: '#3085d6'
        });
    </script>
<?php endif; ?>