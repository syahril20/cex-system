<!-- JS -->
<script src="<?= base_url('assets/js/simple-datatables.min.js') ?>"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const table = document.getElementById("datatablesSimple");
        if (table) {
            new simpleDatatables.DataTable(table, {
                searchable: true,
                fixedHeight: true
            });
        }
    });

    function swalDelete(url) {
        Swal.fire({
            title: 'Hapus Role?',
            text: "Apakah Anda yakin ingin menghapus role ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>

<!-- ✅ Tambahkan CSS fix kolom aksi -->
<style>
    /* Kolom aksi tetap lebar fix */
    .aksi-column {
        min-width: 120px !important;
        max-width: 120px !important;
        white-space: nowrap;
        /* supaya tombol tidak turun */
    }

    /* Jika layar kecil, tombol tetap sejajar (tidak tumpuk) */
    @media (max-width: 576px) {
        .aksi-column {
            min-width: 100px !important;
        }

        .aksi-column .btn {
            padding: 0.25rem 0.4rem;
            font-size: 0.8rem;
        }
    }
</style>