<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">

            <h2 class="mt-4">Selamat Datang di Dashboard Super Admin</h2>

            <div class="row mt-4">
                <!-- Statistik Pengguna -->
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h5>Total Pengguna</h5>
                            <h3><?= $total_users ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <!-- Statistik Admin -->
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <h5>Total Admin</h5>
                            <h3><?= $total_admins ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <!-- Statistik Transaksi -->
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">
                            <h5>Total Transaksi</h5>
                            <h3><?= $total_orders ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <!-- Statistik Laporan -->
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-danger text-white mb-4">
                        <div class="card-body">
                            <h5>Laporan Masuk</h5>
                            <h3><?= $total_reports ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Aktivitas Terbaru -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Aktivitas Terbaru
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php if (!empty($recent_activities)): ?>
                            <?php foreach ($recent_activities as $activity): ?>
                                <li class="list-group-item">
                                    <strong>User ID:</strong> <?= htmlspecialchars($activity['user_id']) ?> <br>
                                    <strong>Action:</strong> <?= htmlspecialchars($activity['action']) ?> <br>
                                    <strong>Description:</strong> <?= htmlspecialchars($activity['description']) ?> <br>
                                    <strong>IP:</strong> <?= htmlspecialchars($activity['ip_address']) ?> <br>
                                    <strong>User Agent:</strong> <?= htmlspecialchars($activity['user_agent']) ?> <br>
                                    <strong>Waktu:</strong> <?= htmlspecialchars($activity['created_at']) ?>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item">Tidak ada aktivitas terbaru.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>
</div>