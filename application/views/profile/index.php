<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">

            <!-- Judul -->
            <h1 class="mt-4 mb-4">
                <i class="fas fa-user-circle me-2 text-primary"></i>
                Profil Saya
            </h1>

            <div class="row g-4">
                <!-- Kolom Kiri: Info User -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-circle text-secondary" style="font-size:5rem;"></i>
                            </div>
                            <h4 class="fw-bold mb-1"><?= htmlspecialchars($session['user']->username) ?></h4>
                            <p class="text-muted mb-2"><?= htmlspecialchars($session['user']->email) ?></p>
                            <span class="badge bg-primary"><?= htmlspecialchars($session['user']->code) ?></span>
                        </div>
                        <div class="card-footer text-center">
                            <small class="text-muted">
                                Dibuat: <?= htmlspecialchars($session['user']->created_at ?? '-') ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Detail Profil & Update Password -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-id-card me-2"></i> Detail Profil
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered align-middle mb-0">
                                <tbody>
                                    <tr>
                                        <th class="bg-light" style="width: 30%;">Username</th>
                                        <td><?= htmlspecialchars($session['user']->username) ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Email</th>
                                        <td><?= htmlspecialchars($session['user']->email) ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Role</th>
                                        <td><?= htmlspecialchars($session['user']->code) ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Dibuat Oleh</th>
                                        <td><?= !empty($session['user']->created_by) ? htmlspecialchars($session['user']->created_by) : 'system' ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Terakhir Diperbarui</th>
                                        <td><?= htmlspecialchars($session['user']->updated_at ?? '-') ?></td>
                                    </tr>
                                    <?php if ($session['user']->code === 'AGENT'): ?>
                                        <tr>
                                            <th class="bg-light">Saldo</th>
                                            <td><strong>Rp <?= number_format($saldo ?? 0, 0, ',', '.') ?></strong></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Form Update Password -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-key me-2"></i> Ubah Password
                        </div>
                        <div class="card-body">
                            <form action="<?= site_url('profile/update_password') ?>" method="post">
                                <div class="mb-3">
                                    <label for="old_password" class="form-label fw-bold">Password Lama</label>
                                    <input type="password" class="form-control" id="old_password" name="old_password"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label fw-bold">Password Baru</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label fw-bold">Konfirmasi Password
                                        Baru</label>
                                    <input type="password" class="form-control" id="confirm_password"
                                        name="confirm_password" required>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-clipboard-list me-2"></i> Aktivitas Terbaru
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_activities)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                        <th>Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_activities as $activity): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($activity['created_at']) ?></td>
                                            <td>
                                                <span
                                                    class="badge bg-primary"><?= htmlspecialchars($activity['action']) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($activity['description']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i> Belum ada aktivitas yang tercatat.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>
</div>