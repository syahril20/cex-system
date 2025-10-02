<style>
    /* Ukuran font tabel lebih kecil */
    .table.table-sm td,
    .table.table-sm th {
        font-size: 0.85rem;
        /* lebih kecil dari default */
        padding: 0.4rem 0.5rem;
        /* rapat tapi tetap nyaman */
    }

    /* Untuk header tabel */
    .table.table-sm thead th {
        font-weight: 600;
        text-transform: capitalize;
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
                            <table id="datatablesSimple"
                                class="table table-bordered table-hover table-sm w-100 align-middle">
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
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Airwaybill</th>
                                        <th>Created At</th>
                                        <th>Created By</th>
                                        <th>Updated At</th>
                                        <th>Updated By</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </tfoot>
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
                                                if ($status === 'Created') {
                                                    $badgeClass = 'bg-warning text-dark';
                                                } elseif ($status === 'Cancelled') {
                                                    $badgeClass = 'bg-danger';
                                                } elseif ($status === 'Complete') {
                                                    $badgeClass = 'bg-success';
                                                } else {
                                                    // Status perjalanan atau sedang berada di mana
                                                    $badgeClass = 'bg-info text-dark';
                                                }
                                                ?>
                                                <span class="badge <?= $badgeClass ?>">
                                                    <?= htmlspecialchars($o['status']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex flex-wrap justify-content-center gap-1">
                                                    <?php if (strtolower($o['status']) === 'Created'): ?>
                                                        <a href="<?= site_url('order/edit/' . $o['id']) ?>"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-primary" disabled>
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                    <?php endif; ?>
                                                    <a href="<?= site_url('order/detail/' . $o['id']) ?>"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                    <?php if (!$o['shipment_image']): ?>
                                                        <a href="<?= site_url('order/upload_form/' . $o['id']) ?>"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="fas fa-upload"></i> Upload
                                                        </a>
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
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/js/datatables-simple-demo.js') ?>"></script>
</div>