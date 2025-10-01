<style>
    .stat-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s;
        cursor: pointer;
        border-radius: 0.75rem; /* rounded-3 */
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    @media (max-width: 576px) {
        .stat-icon {
            font-size: 2rem;
        }
    }

    .table td,
    .table th {
        vertical-align: middle;
    }
</style>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">

            <h2 class="mt-4 mb-4">
                Selamat Datang di <span class="text-primary">Dashboard Agen</span>
            </h2>

            <div class="row mb-4">
                <!-- Total Transaksi -->
                <div class="col-xl-3 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="card bg-primary text-white stat-card shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-semibold">Total Transaksi</h6>
                                <h3 class="fw-bold"><?= isset($total_transaksi) ? $total_transaksi : '0'; ?></h3>
                            </div>
                            <i class="fas fa-exchange-alt stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Saldo Anda -->
                <div class="col-xl-3 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="card bg-success text-white stat-card shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-semibold">Saldo Anda</h6>
                                <h3 class="fw-bold">Rp <?= isset($saldo) ? number_format($saldo, 0, ',', '.') : '0'; ?>
                                </h3>
                            </div>
                            <i class="fas fa-wallet stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Transaksi Hari Ini -->
                <div class="col-xl-3 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="card bg-warning text-dark stat-card shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-semibold">Transaksi Hari Ini</h6>
                                <h3 class="fw-bold"><?= isset($transaksi_hari_ini) ? $transaksi_hari_ini : '0'; ?></h3>
                            </div>
                            <i class="fas fa-calendar-day stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Notifikasi -->
                <div class="col-xl-3 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="card bg-info text-white stat-card shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-semibold">Notifikasi</h6>
                                <h3 class="fw-bold"><?= isset($notifikasi) ? $notifikasi : '0'; ?></h3>
                            </div>
                            <i class="fas fa-bell stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Riwayat & Aktivitas -->
            <div class="row">
                <!-- Riwayat -->
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-history me-2"></i> Riwayat Transaksi Terbaru
                        </div>
                        <div class="card-body">
                            <?php if (isset($riwayat) && count($riwayat) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm w-100 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jenis</th>
                                                <th>Nominal</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jenis</th>
                                                <th>Nominal</th>
                                                <th>Status</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php foreach ($riwayat as $r): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($r['tanggal']); ?></td>
                                                    <td><?= htmlspecialchars($r['jenis']); ?></td>
                                                    <td><strong>Rp <?= number_format($r['nominal'], 0, ',', '.'); ?></strong>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $status = strtolower($r['status']);
                                                        $badgeClass = 'bg-secondary';
                                                        if ($status === 'success' || $status === 'completed')
                                                            $badgeClass = 'bg-success';
                                                        elseif ($status === 'pending')
                                                            $badgeClass = 'bg-warning text-dark';
                                                        elseif ($status === 'failed' || $status === 'cancelled')
                                                            $badgeClass = 'bg-danger';
                                                        ?>
                                                        <span
                                                            class="badge <?= $badgeClass ?>"><?= htmlspecialchars($r['status']); ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <?php $this->load->view('components/empty_table', ['message' => 'Belum ada order yang tercatat.']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Aktivitas -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-secondary text-white">
                            <i class="fas fa-tasks me-2"></i> Aktivitas Terbaru
                        </div>
                        <div class="card-body">
                            <?php if (isset($aktivitas) && count($aktivitas) > 0): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($aktivitas as $a): ?>
                                        <li class="list-group-item d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <?= htmlspecialchars($a); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i> Belum ada aktivitas terbaru.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>
</div>