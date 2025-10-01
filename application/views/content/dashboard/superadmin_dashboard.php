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
                        <?php if (!empty($data['recent_activities'])): ?>
                            <ul>
                                <?php foreach ($data['recent_activities'] as $activity): ?>
                                    <li>
                                        <?php echo htmlspecialchars($activity['user']); ?> -
                                        <?php echo htmlspecialchars($activity['activity']); ?> -
                                        <?php echo htmlspecialchars($activity['timestamp']); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Tidak ada aktivitas terbaru.</p>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>
</div>