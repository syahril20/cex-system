<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4 mb-4">Data User</h1>

            <div class="card shadow-sm mb-4">
                <div
                    class="card-header d-flex flex-wrap justify-content-between align-items-center bg-primary text-white">
                    <div class="mb-2 mb-sm-0">
                        <i class="fas fa-users me-1"></i>
                        Data User Table
                    </div>
                    <a href="<?= site_url('user/create') ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Add New User
                    </a>
                </div>

                <div class="card-body">
                    <?php if ($this->session->userdata('user')->code === 'SUPER_ADMIN'): ?>
                        <div class="table-responsive">
                            <table id="datatablesSimple" class="table table-bordered table-hover align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Updated At</th>
                                        <th>Updated By</th>
                                        <th>Disabled At</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Updated At</th>
                                        <th>Updated By</th>
                                        <th>Disabled At</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php if (!empty($users)): ?>
                                        <?php foreach ($users as $o): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($o['username']) ?></strong></td>
                                                <td><?= htmlspecialchars($o['email']) ?></td>
                                                <td>
                                                    <span class="badge bg-info text-dark"><?= htmlspecialchars($o['role']) ?></span>
                                                </td>
                                                <td><?= htmlspecialchars($o['updated_at']) ?></td>
                                                <td><?= !empty($o['updated_by']) ? htmlspecialchars($o['updated_by']) : '' ?></td>
                                                <td>
                                                    <?php if (!empty($o['disabled_at'])): ?>
                                                        <span class="badge bg-danger"><?= htmlspecialchars($o['disabled_at']) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                                        <!-- Tombol Edit -->
                                                        <a href="<?= site_url('user/edit/' . $o['id']) ?>"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <!-- Tombol Activation -->
                                                        <button type="button" class="btn btn-sm btn-warning btn-activation"
                                                            data-id="<?= $o['id'] ?>"
                                                            data-username="<?= htmlspecialchars($o['username']) ?>"
                                                            data-disabled="<?= !empty($o['disabled_at']) && $o['disabled_at'] !== '0000-00-00 00:00:00' ? '1' : '0' ?>">
                                                            <i class="fas fa-power-off"></i> Activation
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Belum ada user</td>
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

    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/js/datatables-simple-demo.js') ?>"></script>

    <?php $this->load->view('components/user_activation_modal'); ?>
</div>