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

    /* Batasi lebar kolom */
    th:nth-child(1),
    td:nth-child(1) {
        width: 120px;
    }

    /* Airwaybill */
    th:nth-child(6),
    td:nth-child(6) {
        width: 200px;
    }

    /* Status */
    th:nth-child(7),
    td:nth-child(7) {
        width: 180px;
    }

    /* Aksi */

    /* Badge status agar wrap */
    .badge-status {
        white-space: normal;
        word-break: break-word;
        max-width: 200px;
        font-size: 0.75rem;
        padding: 0.35em 0.6em;
    }

    /* Tombol aksi lebih kecil */
    .btn-sm i {
        margin-right: 3px;
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
                        <i class="fas fa-truck me-1"></i>
                        Data Order Table
                    </div>
                    <a href="<?= site_url('order/create') ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> New Order
                    </a>
                </div>

                <div class="card-body">
                    <?php if (!empty($orders)): ?>
                        <div class="table-responsive">
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
                                                $badgeClass = 'bg-secondary';
                                                if ($status == 'created') {
                                                    $badgeClass = 'bg-warning text-dark';
                                                } elseif ($status == 'pending') {
                                                    $badgeClass = 'bg-danger';
                                                } elseif ($status == 'complete') {
                                                    $badgeClass = 'bg-success';
                                                } elseif ($status == 'rejected') {
                                                    $badgeClass = 'bg-danger';
                                                } elseif ($status == 'approved') {
                                                    $badgeClass = 'bg-primary text-white';
                                                } else {
                                                    $badgeClass = 'bg-info text-dark';
                                                }
                                                ?>
                                                <span class="badge badge-status <?= $badgeClass ?>">
                                                    <?= htmlspecialchars($o['status']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex flex-wrap justify-content-center gap-1">
                                                    <?php if (strtolower($o['status']) === 'created'): ?>
                                                        <a href="<?= site_url('order/edit/' . $o['id']) ?>"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>

                                                        <!-- Tombol Process -->
                                                        <button type="button" class="btn btn-sm btn-success btn-process"
                                                            data-id="<?= $o['id'] ?>">
                                                            <i class="fas fa-check-circle"></i> Process
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-primary" disabled>
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-success btn-process" disabled>
                                                            <i class=" fas fa-check-circle"></i> Process
                                                        </button>
                                                    <?php endif; ?>

                                                    <a href="<?= site_url('order/detail/' . $o['id']) ?>"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>

                                                    <?php if (!$o['shipment_image'] && strtolower($o['status']) !== 'rejected'): ?>
                                                        <a href="<?= site_url('order/upload_form/' . $o['id']) ?>"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="fas fa-upload"></i> Upload
                                                        </a>
                                                    <?php elseif (strtolower($o['status']) === 'rejected'): ?>
                                                        <button class="btn btn-sm btn-warning" disabled>
                                                            <i class="fas fa-upload"></i> Upload
                                                        </button>
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
    <script src="<?= base_url('assets/js/datatables-simple-demo.js') ?>"></script>



</div>

    <?php $this->load->view('components/order_process_modal'); ?>