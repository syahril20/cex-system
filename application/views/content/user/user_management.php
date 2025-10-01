<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4 mb-4">Data User</h1>

            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-primary text-white">
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
                                            <?php echo "<script>console.log(" . json_encode($o) . ");</script>"; ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($o['username']) ?></strong></td>
                                                <td><?= htmlspecialchars($o['email']) ?></td>
                                                <td>
                                                    <span class="badge bg-info text-dark"><?= htmlspecialchars($o['role']) ?></span>
                                                </td>
                                                <td><?= htmlspecialchars($o['updated_at']) ?></td>
                                                <td><?= htmlspecialchars($o['updated_by']) ?></td>
                                                <td>
                                                    <?php if ($o['disabled_at']): ?>
                                                        <span class="badge bg-danger"><?= htmlspecialchars($o['disabled_at']) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                                        <a href="<?= site_url('user/edit/' . $o['id']) ?>"
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <?php if ($o['disabled_at'] !== null): ?>
                                                            <a href="<?= site_url('user/activate/' . $o['id']) ?>"
                                                               class="btn btn-sm btn-success">
                                                                <i class="fas fa-check"></i> Activate
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="<?= site_url('user/delete/' . $o['id']) ?>"
                                                               class="btn btn-sm btn-danger btn-delete">
                                                                <i class="fas fa-ban"></i> Disable
                                                            </a>
                                                        <?php endif; ?>
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-delete');
                if (!btn) return;

                e.preventDefault();
                const url = btn.getAttribute('href');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This user will be disabled.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, disable it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
</div>
