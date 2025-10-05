<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4 mb-4">
                <i class="fa-solid fa-user-tag me-2 text-primary"></i>
                Data Role
            </h1>

            <div class="card shadow-sm mb-4">
                <div
                    class="card-header d-flex flex-wrap justify-content-between align-items-center bg-primary text-white">
                    <div class="fw-semibold">
                        <i class="fa-solid fa-table me-1"></i> Data Role Table
                    </div>
                    <a href="<?= site_url('role/create') ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Add New Role
                    </a>
                </div>

                <div class="card-body">
                    <?php if ($user->code === 'SUPER_ADMIN'): ?>
                        <?php if (!empty($roles)): ?>
                            <div class="table-responsive">
                                <table id="datatablesSimple"
                                    class="table table-bordered table-hover table-striped align-middle w-100">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Description</th>
                                            <th>Created At</th>
                                            <th>Created By</th>
                                            <th>Updated At</th>
                                            <th>Updated By</th>
                                            <th class="aksi-column">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($roles as $o): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($o['name']) ?></strong></td>
                                                <td><?= htmlspecialchars($o['code']) ?></td>
                                                <td><?= htmlspecialchars($o['description']) ?></td>
                                                <td><?= htmlspecialchars($o['created_at']) ?></td>
                                                <td><?= htmlspecialchars($o['created_by']) ?></td>
                                                <td><?= htmlspecialchars($o['updated_at']) ?></td>
                                                <td><?= htmlspecialchars($o['updated_by']) ?></td>
                                                <td class="text-center aksi-column">
                                                    <div class="d-flex justify-content-center gap-2 flex-nowrap">
                                                        <a href="<?= site_url('role/edit/' . $o['id']) ?>"
                                                            class="btn btn-sm btn-warning" title="Edit Role">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="swalDelete('<?= site_url('role/delete/' . $o['id']) ?>')"
                                                            title="Delete Role">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> Belum ada role yang tercatat.
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-lock me-2"></i> Anda tidak memiliki akses untuk melihat data role.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>

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

    <?php $this->load->view('components/user_activation_modal'); ?>
</div>