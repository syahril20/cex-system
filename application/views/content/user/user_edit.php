<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">Edit User</h1>
            <div class="card mb-4">
                <div class="card-body">
                    <?php
                    echo "<script>console.log('SYWSAW: ', " . json_encode($roles) . ');</script>';
                    ?>
                    <form action="<?= base_url('user/do_edit/' . $users->id) ?>" method="post">

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?= htmlspecialchars($users->username) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($users->email) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role_id" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role->id) ?>" <?= $role->id == $users->role_id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($role->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password (leave blank if unchanged)</label>
                            <input type="password" class="form-control" id="password" name="password"
                                autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <a href="<?= base_url('user') ?>" class="btn btn-secondary">Cancel</a>
                    </form>
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