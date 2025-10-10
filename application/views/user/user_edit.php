<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4 mb-4">Edit User</h1>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-user-edit me-2"></i> Form Edit User
                </div>
                <div class="card-body">
                    <?php
                    ?>
                    <form action="<?= base_url('user/do_edit/' . $users->id) ?>" method="post">

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label fw-bold">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?= htmlspecialchars($users->username) ?>" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($users->email) ?>" required>
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold">Role</label>
                            <select class="form-select" id="role" name="role_id" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role['id']) ?>" <?= $role['id'] == $users->role_id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($role['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Password
                                <small class="text-muted">(leave blank if unchanged)</small>
                            </label>
                            <input type="password" class="form-control" id="password" name="password"
                                autocomplete="new-password">
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success flex-fill" id="btn-update-user">
                                <i class="fas fa-save me-1"></i> Update User
                            </button>
                            <a href="<?= base_url('user') ?>" class="btn btn-secondary flex-fill">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>

    <!-- DataTables -->
    <script src="<?= base_url('assets/js/simple-datatables.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/datatables-simple-demo.js') ?>"></script>

    <?php $this->load->view('components/user_edit_modal'); ?>
</div>