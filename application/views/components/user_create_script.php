<!-- SweetAlert2 -->
<script src="<?= base_url('assets/js/sweetalert2@11.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form[action*="do_create"]');
        const btn = form.querySelector('button[type="submit"]');

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin simpan user baru?',
                text: "Pastikan data sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, simpan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>