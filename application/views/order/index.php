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
    #agentTable .action-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.25rem;
        flex-wrap: nowrap;
    }

    /* Kolom aksi admin */
    .action-buttons {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.25rem;
        justify-items: center;
        white-space: nowrap;
    }

    /* Pastikan isi tengah & stabil */
    .table td.text-center {
        vertical-align: middle;
        white-space: nowrap;
    }

    /* Kolom aksi agent lebih kecil */
    #agentTable th:last-child,
    #agentTable td:last-child {
        width: 130px !important;
    }

    /* Responsif di layar kecil */
    @media (max-width: 576px) {
        #agentTable .action-buttons {
            flex-wrap: wrap;
        }
    }
</style>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4 mb-4">Data Order</h1>

            <div class="card shadow-sm mb-4">
                <div
                    class="card-header d-flex flex-wrap justify-content-between align-items-center bg-primary text-white">
                    <div class="mb-2 mb-sm-0">
                        <i class="fas fa-truck me-1"></i> Data Order Table
                    </div>
                    <?php if ($user->code == 'AGENT' || $user->code == 'ADMIN'): ?>
                        <a href="<?= site_url('order/create') ?>" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> New Order
                        </a>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <?php if (!empty($orders)): ?>
                        <div class="table-responsive">
                            <?php if ($user->code == 'ADMIN' || $user->code == 'SUPER_ADMIN'): ?>
                                <!-- ================== ADMIN / SUPER_ADMIN TABLE ================== -->
                                <table id="datatablesSimple" class="table table-bordered table-hover table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Airwaybill</th>
                                            <th>Created At</th>
                                            <th>Created By</th>
                                            <th>Updated At</th>
                                            <th>Updated By</th>
                                            <th>Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $o): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($o['airwaybill']) ?></strong></td>
                                                <td><?= htmlspecialchars($o['created_at']) ?></td>
                                                <td><?= htmlspecialchars($o['created_by']) ?></td>
                                                <td><?= htmlspecialchars($o['updated_at']) ?></td>
                                                <td><?= !empty($o['updated_by']) ? htmlspecialchars($o['updated_by']) : '-' ?></td>
                                                <td>
                                                    <?php
                                                    $status = strtolower($o['status']);
                                                    switch ($status) {
                                                        case 'pending':
                                                            $badgeClass = 'bg-warning text-dark';
                                                            break;
                                                        case 'complete':
                                                            $badgeClass = 'bg-success';
                                                            break;
                                                        case 'rejected':
                                                            $badgeClass = 'bg-danger';
                                                            break;
                                                        case 'approved':
                                                            $badgeClass = 'bg-primary text-white';
                                                            break;
                                                        default:
                                                            $badgeClass = 'bg-info text-dark';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge badge-status <?= $badgeClass ?>">
                                                        <?= htmlspecialchars($o['status']) ?>
                                                    </span>
                                                </td>

                                                <!-- Tombol Aksi -->
                                                <td class="text-center">
                                                    <div class="action-buttons">
                                                        <?php if (strtolower($o['status']) === 'created'): ?>
                                                            <a href="<?= site_url('order/edit/' . $o['id']) ?>"
                                                                class="btn btn-sm btn-primary">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-success btn-process"
                                                                data-id="<?= $o['id'] ?>">
                                                                <i class="fas fa-check-circle"></i> Process
                                                            </button>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-primary" disabled>
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                            <button class="btn btn-sm btn-success" disabled>
                                                                <i class="fas fa-check-circle"></i> Process
                                                            </button>
                                                        <?php endif; ?>

                                                        <a href="<?= site_url('order/detail/' . $o['id']) ?>"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> Detail
                                                        </a>

                                                        <?php if (
                                                            !$o['shipment_image'] && strtolower($o['status']) !== 'rejected'
                                                            && $user->code !== 'SUPER_ADMIN'
                                                        ): ?>
                                                            <a href="<?= site_url('order/upload_form/' . $o['id']) ?>"
                                                                class="btn btn-sm btn-warning">
                                                                <i class="fas fa-upload"></i> Upload
                                                            </a>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-warning" disabled>
                                                                <i class="fas fa-upload"></i> Upload
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <!-- ================== AGENT TABLE ================== -->
                                <table id="agentTable" class="table table-bordered table-hover table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Airwaybill</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $o): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($o['airwaybill']) ?></strong></td>
                                                <td><?= htmlspecialchars($o['created_at']) ?></td>
                                                <td>
                                                    <?php
                                                    $status = strtolower($o['status']);
                                                    switch ($status) {
                                                        case 'pending':
                                                            $badgeClass = 'bg-warning text-dark';
                                                            break;
                                                        case 'complete':
                                                            $badgeClass = 'bg-success';
                                                            break;
                                                        case 'rejected':
                                                            $badgeClass = 'bg-danger';
                                                            break;
                                                        case 'approved':
                                                            $badgeClass = 'bg-primary text-white';
                                                            break;
                                                        default:
                                                            $badgeClass = 'bg-info text-dark';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge badge-status <?= $badgeClass ?>">
                                                        <?= htmlspecialchars($o['status']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="action-buttons">
                                                        <a href="<?= site_url('order/detail/' . $o['id']) ?>"
                                                            class="btn btn-sm btn-primary d-inline-flex align-items-center">
                                                            <i class="fas fa-eye fa-sm me-1"></i> Detail
                                                        </a>

                                                        <?php if (!$o['shipment_image'] && strtolower($o['status']) !== 'rejected'): ?>
                                                            <a href="<?= site_url('order/upload_form/' . $o['id']) ?>"
                                                                class="btn btn-sm btn-warning">
                                                                <i class="fas fa-upload"></i> Upload
                                                            </a>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-warning" disabled>
                                                                <i class="fas fa-upload"></i> Upload
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php $this->load->view('components/empty_table', ['message' => 'Belum ada order yang tercatat.']); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>

    <!-- DataTables -->
    <script src="<?= base_url('assets/js/simple-datatables.min.js') ?>"></script>
    <script>
        // Pastikan kedua tabel diinisialisasi
        document.addEventListener("DOMContentLoaded", function () {
            if (document.querySelector("#datatablesSimple")) {
                new simpleDatatables.DataTable("#datatablesSimple");
            }
            if (document.querySelector("#agentTable")) {
                new simpleDatatables.DataTable("#agentTable");
            }
        });
    </script>
</div>

<?php $this->load->view('components/order_process_modal'); ?>