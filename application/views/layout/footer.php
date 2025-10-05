<style>
    footer {
        font-size: 0.85rem;
        box-shadow: 0 -1px 4px rgba(0, 0, 0, 0.08);
        background-color: #f8f9fa;
        /* jaga konsistensi bg dengan class bg-light */
    }

    footer a {
        text-decoration: none;
        color: var(--bs-primary, #0d6efd);
        /* lebih fleksibel */
        transition: color 0.2s ease-in-out, text-decoration 0.2s ease-in-out;
    }

    footer a:hover {
        color: var(--bs-primary-hover, #0a58ca);
        text-decoration: underline;
    }

    @media (max-width: 576px) {
        footer .d-flex {
            flex-direction: column;
            text-align: center;
            gap: 0.5rem;
        }
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