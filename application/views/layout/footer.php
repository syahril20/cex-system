<style>
    footer {
        font-size: 0.85rem;
        box-shadow: 0 -1px 4px rgba(0,0,0,0.08);
    }
    footer a {
        text-decoration: none;
        color: #0d6efd;
        transition: color 0.2s ease-in-out;
    }
    footer a:hover {
        color: #0a58ca;
        text-decoration: underline;
    }
</style>

<footer class="py-3 bg-light mt-auto">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">&copy; <?= date('Y') ?> Cex. All rights reserved.</div>
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
