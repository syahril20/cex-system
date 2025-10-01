<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">

            <h2 class="mt-4 mb-4">Selamat Datang di <span class="text-primary">Dashboard Agen</span></h2>

            <!-- Statistik -->
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">Total Transaksi</h6>
                            <h3 class="fw-bold"><?= isset($total_transaksi) ? $total_transaksi : '0'; ?></h3>
                            <small class="text-white-50">Jumlah transaksi yang telah Anda lakukan</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">Saldo Anda</h6>
                            <h3 class="fw-bold">Rp <?= isset($saldo) ? number_format($saldo, 0, ',', '.') : '0'; ?></h3>
                            <small class="text-white-50">Saldo yang tersedia untuk transaksi</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 bg-warning text-dark">
                        <div class="card-body">
                            <h6 class="card-title">Transaksi Hari Ini</h6>
                            <h3 class="fw-bold"><?= isset($transaksi_hari_ini) ? $transaksi_hari_ini : '0'; ?></h3>
                            <small class="text-dark">Transaksi yang Anda lakukan hari ini</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">Notifikasi</h6>
                            <h3 class="fw-bold"><?= isset($notifikasi) ? $notifikasi : '0'; ?></h3>
                            <small class="text-white-50">Pesan penting untuk Anda</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat & Aktivitas -->
            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-history me-2"></i> Riwayat Transaksi Terbaru
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Jenis</th>
                                            <th>Nominal</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(isset($riwayat) && count($riwayat) > 0): ?>
                                            <?php foreach($riwayat as $r): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($r['tanggal']); ?></td>
                                                    <td><?= htmlspecialchars($r['jenis']); ?></td>
                                                    <td><strong>Rp <?= number_format($r['nominal'], 0, ',', '.'); ?></strong></td>
                                                    <td>
                                                        <?php 
                                                            $status = strtolower($r['status']);
                                                            $badgeClass = 'bg-secondary';
                                                            if ($status === 'success' || $status === 'completed') $badgeClass = 'bg-success';
                                                            elseif ($status === 'pending') $badgeClass = 'bg-warning text-dark';
                                                            elseif ($status === 'failed' || $status === 'cancelled') $badgeClass = 'bg-danger';
                                                        ?>
                                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($r['status']); ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Belum ada transaksi.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aktivitas -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-secondary text-white">
                            <i class="fas fa-tasks me-2"></i> Aktivitas Terbaru
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php if(isset($aktivitas) && count($aktivitas) > 0): ?>
                                    <?php foreach($aktivitas as $a): ?>
                                        <li class="list-group-item d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <?= htmlspecialchars($a); ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-muted">
                                        <i class="fas fa-info-circle me-2"></i> Belum ada aktivitas terbaru.
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>
</div>
