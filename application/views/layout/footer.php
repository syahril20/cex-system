<style>
    footer {
        font-size: 0.85rem;
        box-shadow: 0 -1px 4px rgba(0, 0, 0, 0.08);
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

<?php $this->load->view('components/alert'); ?>