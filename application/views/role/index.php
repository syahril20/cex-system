<style>
    /* Font kecil untuk tabel */
    .table.table-sm td,
    .table.table-sm th {
        font-size: 0.85rem;
        padding: 0.45rem 0.5rem;
    }

    /* Header tabel */
    .table thead th {
        font-weight: 600;
        text-transform: capitalize;
    }

    /* Lebar kolom umum */
    th:nth-child(1),
    td:nth-child(1) {
        width: 120px;
    }

    th:nth-child(6),
    td:nth-child(6) {
        width: 200px;
    }

    th:nth-child(7),
    td:nth-child(7) {
        width: 180px;
    }

    /* Badge status */
    .badge-status {
        white-space: normal;
        word-break: break-word;
        max-width: 200px;
        font-size: 0.75rem;
        padding: 0.35em 0.6em;
    }

    /* Tombol aksi kecil & stabil */
    .table td .btn-sm {
        min-width: 70px;
        white-space: nowrap;
        font-size: 0.7rem;
        padding: 0.25rem 0.35rem;
        transition: none !important;
    }

    /* Ikon tombol */
    .table td .btn-sm i {
        margin-right: 3px;
        font-size: 0.75rem;
    }

    /* Kolom aksi — fleksibel horizontal (AGENT) */
    #roleTable .action-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.25rem;
        flex-wrap: nowrap;
    }

    /* Pastikan isi tengah & stabil */
    .table td.text-center {
        vertical-align: middle;
        white-space: nowrap;
    }

    /* Kolom aksi agent lebih kecil */
    #roleTable th:last-child,
    #roleTable td:last-child {
        width: 130px !important;
    }

    /* Responsif di layar kecil */
    @media (max-width: 576px) {
        #roleTable .action-buttons {
            flex-wrap: wrap;
        }
    }
</style>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4 mb-4">
                Data Role
            </h1>

            <div class="card shadow-sm mb-4">
                <div
                    class="card-header d-flex flex-wrap justify-content-between align-items-center bg-primary text-white">
                    <div class="mb-2 mb-sm-0">
                        Data Role Table
                    </div>
                    <a href="<?= site_url('role/create') ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Add New Role
                    </a>
                </div>

                <div class="card-body">
                    <?php if ($user->code === 'SUPER_ADMIN'): ?>
                        <?php if (!empty($roles)): ?>
                            <div class="table-responsive">
                                <table id="roleTable" class="table table-bordered table-hover table-sm align-middle">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Description</th>
                                            <th>Created At</th>
                                            <th>Created By</th>
                                            <th>Updated At</th>
                                            <th>Updated By</th>
                                            <th class="text-center">Aksi</th>
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
                                                <td class="text-center">
                                                    <div class="action-buttons">
                                                        <a href="<?= site_url('role/edit/' . $o['id']) ?>"
                                                            class="btn btn-sm btn-primary" title="Edit Role">
                                                            <i class="fas fa-edit fa-sm me-1"></i>Edit
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="swalDelete('<?= site_url('role/delete/' . $o['id']) ?>')"
                                                            title="Delete Role">
                                                            <i class="fas fa-trash-alt"></i>Delete
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
    <?php $this->load->view('components/role_script'); ?>

    <?php $this->load->view('components/user_activation_modal'); ?>
</div>