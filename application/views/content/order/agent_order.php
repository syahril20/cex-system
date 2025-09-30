<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Data Order</h1>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-table me-1"></i>
                        DataTable
                    </div>
                    <a href="<?= site_url('order/create') ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> New Order
                    </a>
                </div>

                <div class="card-body">

                    <?php
                    if ($this->session->userdata('user')->code === 'AGENT'): ?>
                        <table id="datatablesSimple">
                            <thead>
                                <tr>
                                    <th>Airwaybill</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Airwaybill</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $o): ?>
                                        <tr>
                                            <td><?= $o['airwaybill'] ?></td>
                                            <td><?= $o['created_at'] ?></td>
                                            <td><?= $o['status'] ?></td>
                                            <td>
                                                <a href="<?= site_url('order/detail/' . $o['id']) ?>"
                                                    class="btn btn-sm btn-primary">Detail</a>
                                                <?php
                                                if (!$o['shipment_image']):
                                                    ?>
                                                    <a href="<?= site_url('order/upload_form/' . $o['id']) ?>"
                                                        class="btn btn-sm btn-warning">Upload Image</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada order</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                </div>

            </div>
        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/js/datatables-simple-demo.js') ?>"></script>
</div>