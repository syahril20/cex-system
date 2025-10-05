<!-- SweetAlert2 -->
<script src="<?= base_url('assets/js/sweetalert2@11.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        renderShipmentDetails();
        const form = document.querySelector('form[action*="do_edit"]');
        const btn = document.getElementById('btn-update-order');

        if (form && btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Update Order?',
                    text: "Pastikan data sudah benar.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, update!',
                    background: 'rgba(255, 255, 255, 0.75)', // semi transparan putih
                    customClass: {
                        popup: 'swal-ios-popup'
                    }
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        }
    });
</script>

<style>
    /* Backdrop ala iOS glass */
    .swal2-backdrop-show {
        backdrop-filter: blur(12px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(12px) saturate(180%) !important;
        background-color: rgba(255, 255, 255, 0.25) !important;
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