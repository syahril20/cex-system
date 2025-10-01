<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4 mb-4">Data Order</h1>

            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <div>
                        <i class="fas fa-box me-1"></i>
                        Data Order Table
                    </div>
                    <a href="<?= site_url('order/create') ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-plus fa-sm me-1"></i> New Order
                    </a>
                </div>

                <div class="card-body">
                    <?php if ($this->session->userdata('user')->code === 'AGENT'): ?>
                        <div class="table-responsive">
                            <table id="datatablesSimple" class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Airwaybill</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Airwaybill</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php if (!empty($orders)): ?>
                                        <?php foreach ($orders as $o): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($o['airwaybill']) ?></strong></td>
                                                <td><?= htmlspecialchars($o['created_at']) ?></td>
                                                <td>
                                                    <?php 
                                                        $status = strtolower($o['status']);
                                                        $badgeClass = 'bg-secondary';
                                                        if ($status === 'success' || $status === 'completed') $badgeClass = 'bg-success';
                                                        elseif ($status === 'pending') $badgeClass = 'bg-warning text-dark';
                                                        elseif ($status === 'failed' || $status === 'cancelled') $badgeClass = 'bg-danger';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= htmlspecialchars($o['status']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <!-- Tombol Detail -->
                                                    <a href="<?= site_url('order/detail/' . $o['id']) ?>"
                                                       class="btn btn-sm btn-primary me-1 d-inline-flex align-items-center">
                                                        <i class="fas fa-eye fa-sm me-1"></i> Detail
                                                    </a>

                                                    <!-- Tombol Upload Image -->
                                                    <?php if (!$o['shipment_image']): ?>
                                                        <a href="<?= site_url('order/upload_form/' . $o['id']) ?>"
                                                           class="btn btn-sm btn-warning d-inline-flex align-items-center">
                                                            <i class="fas fa-upload fa-sm me-1"></i> Upload Image
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Belum ada order</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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
