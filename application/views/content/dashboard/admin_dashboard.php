<style>
    .stat-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s;
        cursor: pointer;
        border-radius: 0.75rem;
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

            <!-- Judul Dashboard -->
            <h1 class="mt-4 mb-4">
                <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                Dashboard Admin
            </h1>

            <!-- Statistik -->
            <div class="row mb-4">
                <!-- Total Agen -->
                <div class="col-xl-3 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="card bg-primary text-white stat-card shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-semibold">Total Agen</h6>
                                <h3 class="fw-bold"><?= $total_agents ?? 0 ?></h3>
                            </div>
                            <i class="fas fa-users stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Transaksi -->
                <div class="col-xl-3 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="card bg-success text-white stat-card shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-semibold">Total Transaksi</h6>
                                <h3 class="fw-bold"><?= $total_orders ?? 0 ?></h3>
                            </div>
                            <i class="fas fa-exchange-alt stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Transaksi Pending -->
                <div class="col-xl-3 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="card bg-warning text-dark stat-card shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-semibold">Transaksi Pending</h6>
                                <h3 class="fw-bold"><?= $total_pending ?? 0 ?></h3>
                            </div>
                            <i class="fas fa-clock stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Laporan / Notifikasi -->
                <div class="col-xl-3 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="card bg-danger text-white stat-card shadow-sm h-100">
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
                    <?php if (!empty($recent_activities)): ?>
                        <div class="table-responsive">
                            <table id="datatablesSimple" class="table table-bordered table-hover table-sm w-100 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Username</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Username</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>Created At</th>
                                    </tr>
                                </tfoot>
                                <tbody>
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
                                            <td><?= htmlspecialchars($activity['created_at']) ?></td>
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

    <!-- DataTables -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const table = document.getElementById("datatablesSimple");
            if (table) {
                new simpleDatatables.DataTable(table, {
                    searchable: true,
                    sortable: false
                });
            }
        });
    </script>
</div>
