<script src="<?= base_url('assets/js/sweetalert2@11.js') ?>"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Inisialisasi DataTables
        if (document.querySelector("#datatablesSimple")) {
            new simpleDatatables.DataTable("#datatablesSimple");
        }
        if (document.querySelector("#agentTable")) {
            new simpleDatatables.DataTable("#agentTable");
        }

        // Status badge click: tampilkan riwayat status sebagai timeline
        const badges = document.querySelectorAll('.track-link');
        badges.forEach(badge => {
            badge.addEventListener('click', function () {
                const historyJson = this.getAttribute('data-history');
                if (!historyJson) return;

                const history = JSON.parse(historyJson);

                let htmlContent = `
            <div style="max-height:220px;overflow-y:auto;">
                <ul class="timeline" style="list-style:none;padding-left:0;">
            `;
                history.forEach((h, idx) => {
                    htmlContent += `
                <li style="position:relative;padding-left:28px;margin-bottom:18px;">
                <span style="
                    position:absolute;
                    left:0;
                    top:2px;
                    width:16px;
                    height:16px;
                    border-radius:50%;
                    background:${idx === 0 ? '#0d6efd' : '#adb5bd'};
                    border:2px solid #fff;
                    box-shadow:0 0 0 2px #dee2e6;
                    display:inline-block;
                "></span>
                <div>
                    <span style="font-weight:600;">${h.status}</span>
                    <br>
                    <small style="color:#6c757d;">${h.date}</small>
                </div>
                </li>
                `;
                });
                htmlContent += `
                </ul>
            </div>
            <style>
                .timeline li:not(:last-child):after {
                content: '';
                position: absolute;
                left:7px;
                top:22px;
                width:2px;
                height:calc(100% - 22px);
                background:#dee2e6;
                }
            </style>
            `;

                Swal.fire({
                    title: `<span style="font-size:1.1rem;">Riwayat Status <span class="badge bg-info"></span></span>`,
                    html: htmlContent,
                    icon: 'info',
                    confirmButtonText: 'Tutup',
                    width: 420,
                    background: 'rgba(255, 255, 255, 0.75)', // semi transparan putih
                    customClass: {
                        popup: 'swal-ios-popup'
                    }
                });
            });
        });
    });

    document.addEventListener("click", function (e) {
        if (e.target.closest(".btn-process")) {
            const btn = e.target.closest(".btn-process");
            const orderId = btn.getAttribute("data-id");

            Swal.fire({
                title: 'Process Order?',
                text: "Pilih Approve atau Reject untuk order ini.",
                icon: 'question',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: '<i class="fas fa-check"></i> Approve',
                denyButtonText: '<i class="fas fa-times"></i> Reject',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745',
                denyButtonColor: '#dc3545',
                background: 'rgba(255, 255, 255, 0.75)', // semi transparan putih
                customClass: {
                    popup: 'swal-ios-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Approving...',
                        text: 'Sedang memproses approval order.',
                        allowOutsideClick: false,
                        background: 'rgba(255, 255, 255, 0.75)',
                        customClass: {
                            popup: 'swal-ios-popup'
                        },
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        window.location.href = "<?= site_url('order/approve/') ?>" + orderId;
                    }, 1500);
                } else if (result.isDenied) {
                    Swal.fire({
                        title: 'Rejecting...',
                        text: 'Sedang memproses penolakan order.',
                        allowOutsideClick: false,
                        background: 'rgba(255, 255, 255, 0.75)',
                        customClass: {
                            popup: 'swal-ios-popup'
                        },
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        window.location.href = "<?= site_url('order/reject/') ?>" + orderId;
                    }, 1500);
                }
            });
        }
    });

    document.addEventListener("click", function (e) {
        if (e.target.closest(".btn-cancel")) {
            const btn = e.target.closest(".btn-cancel");
            const orderId = btn.getAttribute("data-id");

            Swal.fire({
                title: 'Cancel Order?',
                text: "Pilih Batalkan atau Tetapkan order ini.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-times"></i> Batalkan',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
                background: 'rgba(255, 255, 255, 0.75)', // semi transparan putih
                customClass: {
                    popup: 'swal-ios-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Cancelling...',
                        text: 'Sedang memproses pembatalan order.',
                        allowOutsideClick: false,
                        background: 'rgba(255, 255, 255, 0.75)',
                        customClass: {
                            popup: 'swal-ios-popup'
                        },
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        window.location.href = "<?= site_url('order/cancel/') ?>" + orderId;
                    }, 1500);
                }
            });
        }
    });

</script>

<style>
    /* Backdrop ala iOS */
    .swal2-backdrop-show {
        backdrop-filter: blur(12px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(12px) saturate(180%) !important;
        background-color: rgba(255, 255, 255, 0.25) !important;
        /* putih transparan */
    }

    /* Popup ala kaca iOS */
    .swal-ios-popup {
        border-radius: 20px !important;
        backdrop-filter: blur(15px) saturate(200%) !important;
        -webkit-backdrop-filter: blur(15px) saturate(200%) !important;
        background-color: rgba(255, 255, 255, 0.65) !important;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2) !important;
    }
</style>