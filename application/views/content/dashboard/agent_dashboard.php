<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">

        <h2 class="mt-4">Selamat Datang di Dashboard Agen</h2>
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Transaksi</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= isset($total_transaksi) ? $total_transaksi : '0'; ?></h5>
                        <p class="card-text">Jumlah transaksi yang telah Anda lakukan.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Saldo Anda</div>
                    <div class="card-body">
                        <h5 class="card-title">Rp <?= isset($saldo) ? number_format($saldo, 0, ',', '.') : '0'; ?></h5>
                        <p class="card-text">Saldo yang tersedia untuk transaksi.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Transaksi Hari Ini</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= isset($transaksi_hari_ini) ? $transaksi_hari_ini : '0'; ?></h5>
                        <p class="card-text">Transaksi yang Anda lakukan hari ini.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Notifikasi</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= isset($notifikasi) ? $notifikasi : '0'; ?></h5>
                        <p class="card-text">Pesan penting untuk Anda.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-8">
                <h4>Riwayat Transaksi Terbaru</h4>
                <table class="table table-bordered table-striped">
                    <thead>
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
                                    <td><?= $r['tanggal']; ?></td>
                                    <td><?= $r['jenis']; ?></td>
                                    <td>Rp <?= number_format($r['nominal'], 0, ',', '.'); ?></td>
                                    <td><?= $r['status']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">Belum ada transaksi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <h4>Aktivitas Terbaru</h4>
                <ul class="list-group">
                    <?php if(isset($aktivitas) && count($aktivitas) > 0): ?>
                        <?php foreach($aktivitas as $a): ?>
                            <li class="list-group-item"><?= $a; ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item">Belum ada aktivitas terbaru.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
            
        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>
</div>