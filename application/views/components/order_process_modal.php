<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
</script>

<style>
    /* Backdrop ala iOS */
    .swal2-backdrop-show {
        backdrop-filter: blur(12px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(12px) saturate(180%) !important;
        background-color: rgba(255, 255, 255, 0.25) !important; /* putih transparan */
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
