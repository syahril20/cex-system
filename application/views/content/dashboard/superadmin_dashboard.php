<style>
    .stat-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s;
        cursor: pointer;
        border-radius: 0.75rem; /* rounded-3 */
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 8px 15px rgba(0,0,0,0.2);
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
    .table td, .table th {
        vertical-align: middle;
    }
</style>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">

            <!-- Judul Dashboard -->
            <h1 class="mt-4 mb-4">
                <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                Dashboard Super Admin
            </h1>

            <!-- Statistik -->
            <div class="row mb-4">
                <!-- Statistik Pengguna -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-primary text-white stat-card shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-semibold">Total Pengguna</h6>
                                <h3 class="fw-bold"><?= $total_users ?? 0 ?></h3>
                            </div>
                            <i class="fas fa-users stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Statistik Admin -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-success text-white stat-card shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-semibold">Total Admin</h6>
                                <h3 class="fw-bold"><?= $total_admin ?? 0 ?></h3>
                            </div>
                            <i class="fas fa-user-shield stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Statistik Transaksi -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-warning text-dark stat-card shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-semibold">Total Transaksi</h6>
                                <h3 class="fw-bold"><?= $total_orders ?? 0 ?></h3>
                            </div>
                            <i class="fas fa-exchange-alt stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Statistik Laporan -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-danger text-white stat-card shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-semibold">Laporan Masuk</h6>
                                <h3 class="fw-bold"><?= $total_reports ?? 0 ?></h3>
                            </div>
                            <i class="fas fa-flag stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h6 class="m-0 fw-semibold">
                        <i class="fas fa-clipboard-list me-2"></i> Activity Log
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Username</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_activities)): ?>
                                    <?php foreach ($recent_activities as $activity): ?>
                                        <tr>
                                            <td>
                                                <i class="fas fa-user-circle text-secondary me-1"></i>
                                                <?= htmlspecialchars($activity['username']) ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-dark">
                                                    <?= htmlspecialchars($activity['action']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($activity['description']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            Tidak ada data aktivitas.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>
</div>
