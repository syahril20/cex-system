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
                        <?php if (!empty($orders)): ?>
                            <div class="table-responsive">
                                <table id="datatablesSimple" class="table table-bordered table-hover align-middle w-100">
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
                                                <!-- Airwaybill -->
                                                <td><strong><?= htmlspecialchars($o['airwaybill']) ?></strong></td>

                                                <!-- Created At -->
                                                <td><?= htmlspecialchars($o['created_at']) ?></td>

                                                <!-- Status -->
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
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= htmlspecialchars($o['status']) ?>
                                                    </span>
                                                </td>

                                                <!-- Aksi -->
                                                <td class="text-center">
                                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                                        <!-- Tombol Detail -->
                                                        <a href="<?= site_url('order/detail/' . $o['id']) ?>"
                                                            class="btn btn-sm btn-primary d-inline-flex align-items-center">
                                                            <i class="fas fa-eye fa-sm me-1"></i> Detail
                                                        </a>

                                                        <!-- Tombol Upload Image -->
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