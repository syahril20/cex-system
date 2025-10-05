<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">

            <!-- Judul -->
            <h1 class="mt-4 mb-4">
                <i class="fas fa-user-plus me-2 text-success"></i>
                Tambah User
            </h1>

            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-success text-white">
                    <div><i class="fas fa-user-plus me-2"></i> Form Tambah User</div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('user/do_create') ?>" method="post">

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label fw-bold">Username</label>
                            <input type="text" class="form-control shadow-sm" id="username" name="username" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control shadow-sm" id="email" name="email" required>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Password</label>
                            <input type="password" class="form-control shadow-sm" id="password" name="password" required>
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold">Role</label>
                            <select class="form-select shadow-sm" id="role" name="role_id" required>
                                <option value="">Pilih Role</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role->id ?>"><?= htmlspecialchars($role->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-success flex-fill">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                            <a href="<?= base_url('user') ?>" class="btn btn-secondary flex-fill">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>

    <!-- SweetAlert2 -->
    <script src="<?= base_url('assets/js/sweetalert2@11.js') ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[action*="do_create"]');
            const btn = form.querySelector('button[type="submit"]');
            
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin simpan user baru?',
                    text: "Pastikan data sudah benar.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, simpan!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</div>
