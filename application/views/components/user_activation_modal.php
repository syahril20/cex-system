<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("click", function (e) {
        if (e.target.closest(".btn-activation")) {
            e.preventDefault();
            const btn = e.target.closest(".btn-activation");
            const userId = btn.getAttribute("data-id");
            const username = btn.getAttribute("data-username");
            const isDisabled = btn.getAttribute("data-disabled") === "1"; // 1 = sudah nonaktif

            Swal.fire({
                title: 'Pilih Aksi',
                text: "Apa yang ingin dilakukan untuk user " + username + "?",
                icon: 'question',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Active',
                denyButtonText: 'Disable',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745',
                denyButtonColor: '#d33',
                background: 'rgba(255, 255, 255, 0.75)',
                customClass: {
                    popup: 'swal-ios-popup'
                },
                didOpen: () => {
                    const confirmBtn = Swal.getConfirmButton(); // tombol Active
                    const denyBtn = Swal.getDenyButton();       // tombol Disable

                    if (isDisabled) {
                        // User sudah nonaktif → Disable tidak bisa dipilih
                        denyBtn.disabled = true;
                    } else {
                        // User masih aktif → Active tidak bisa dipilih
                        confirmBtn.disabled = true;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Klik Active → tampilkan loading sebelum redirect
                    Swal.fire({
                        title: 'Mengaktifkan...',
                        text: 'Mohon tunggu',
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
                        window.location.href = "<?= site_url('user/activate/') ?>" + userId;
                    }, 1000);
                } else if (result.isDenied) {
                    // Klik Disable → tampilkan loading sebelum redirect
                    Swal.fire({
                        title: 'Menonaktifkan...',
                        text: 'Mohon tunggu',
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
                        window.location.href = "<?= site_url('user/delete/') ?>" + userId;
                    }, 1000);
                }
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
