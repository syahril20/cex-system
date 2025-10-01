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
                    <a href="<?= site_url('user/create') ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Add New User
                    </a>
                </div>

                <div class="card-body">

                    <?php
                    if ($this->session->userdata('user')->code === 'SUPER_ADMIN'): ?>
                        <table id="datatablesSimple">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Updated At</th>
                                    <th>Updated By</th>
                                    <th>Disabled At</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Updated At</th>
                                    <th>Updated By</th>
                                    <th>Disabled At</th>
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $o): ?>
                                        <?php echo "<script>console.log(" . json_encode($o) . ");</script>"; ?>
                                        <tr>
                                            <td><?= $o['username'] ?></td>
                                            <td><?= $o['email'] ?></td>
                                            <td><?= $o['role'] ?></td>
                                            <td><?= $o['updated_at'] ?></td>
                                            <td><?= $o['updated_by'] ?></td>
                                            <td><?= $o['disabled_at'] ?></td>
                                            <td>
                                                <a href="<?= site_url('user/edit/' . $o['id']) ?>"
                                                    class="btn btn-sm btn-primary">Edit</a>
                                                <?php if ($o['disabled_at'] !== null): ?>
                                                    <a href="<?= site_url('user/activate/' . $o['id']) ?>"
                                                        class="btn btn-sm btn-success">Activate</a>
                                                <?php else: ?>
                                                    <a href="<?= site_url('user/delete/' . $o['id']) ?>"
                                                        class="btn btn-sm btn-danger btn-delete">Delete</a>
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

    <!-- SweetAlert2 harus di-include -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // pakai body delegation
            document.body.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-delete');
                if (!btn) return;

                e.preventDefault(); // hentikan redirect

                const url = btn.getAttribute('href');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>

</div>