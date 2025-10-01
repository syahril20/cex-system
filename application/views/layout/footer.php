<footer class="py-4 bg-light mt-auto">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Copyright &copy; Cex 2025</div>
            <div>
                <a href="#">Privacy Policy</a>
                &middot;
                <a href="#">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($this->session->flashdata('swal')): ?>
    <script>
        Swal.fire({
            title: '<?= $this->session->flashdata("swal")["title"] ?>',
            text: '<?= $this->session->flashdata("swal")["text"] ?>',
            icon: '<?= $this->session->flashdata("swal")["icon"] ?>',
            confirmButtonText: 'OK'
        });
    </script>
<?php endif; ?>