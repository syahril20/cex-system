<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .accordion-button {
        font-size: 0.9rem;
    }

    .accordion-body th {
        color: #495057;
        font-weight: 500;
    }

    .img-thumbnail {
        border: 2px solid #dee2e6;
    }
</style>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4 mb-4">Detail Order</h1>

            <?php
            $id_order = $this->uri->segment(3);
            $query = $this->db->get_where('orders', ['id' => $id_order]);
            $order = $query->row_array();
            ?>

            <?php if ($order): ?>
                <!-- Card: Informasi Order -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-primary text-white">
                        <div><i class="fas fa-info-circle me-2"></i> Informasi Order</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-striped mb-0 w-100">
                                <tr>
                                    <th style="width:180px;">ID Order</th>
                                    <td><?= htmlspecialchars($order['id']) ?></td>
                                </tr>
                                <tr>
                                    <th>User ID</th>
                                    <td><?= htmlspecialchars($order['user_id']) ?></td>
                                </tr>
                                <tr>
                                    <th>Airwaybill</th>
                                    <td><?= htmlspecialchars($order['airwaybill']) ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php
                                        $status = strtolower($order['status']);
                                        $badgeClass = 'bg-secondary';
                                        if ($status === 'success' || $status === 'completed')
                                            $badgeClass = 'bg-success';
                                        elseif ($status === 'pending')
                                            $badgeClass = 'bg-warning text-dark';
                                        elseif ($status === 'failed' || $status === 'cancelled')
                                            $badgeClass = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($order['status']) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td><?= htmlspecialchars($order['updated_at']) ?></td>
                                </tr>
                                <tr>
                                    <th>Created By</th>
                                    <td><?= htmlspecialchars($order['created_by']) ?></td>
                                </tr>
                                <tr>
                                    <th>Updated By</th>
                                    <td><?= htmlspecialchars($order['updated_by'] ?? '') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Card: Data Pengiriman -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-secondary text-white">
                        <div><i class="fas fa-database me-2"></i> Data Pengiriman</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm mb-0 w-100">
                                <?php
                                $data = json_decode($order['data'], true);
                                if (is_array($data)):
                                    foreach ($data as $key => $value):
                                        if ($key === 'shipment_details' && is_array($value)): ?>
                                            <tr>
                                                <th style="width:180px;">Shipment Details</th>
                                                <td>
                                                    <div class="accordion" id="accordionShipment">
                                                        <?php foreach ($value as $idx => $detail):
                                                            $collapseId = 'shipmentDetail' . $idx; ?>
                                                            <div class="accordion-item mb-2">
                                                                <h2 class="accordion-header" id="heading<?= $collapseId ?>">
                                                                    <button class="accordion-button collapsed py-2" type="button"
                                                                        data-bs-toggle="collapse" data-bs-target="#collapse<?= $collapseId ?>"
                                                                        aria-expanded="false" aria-controls="collapse<?= $collapseId ?>">
                                                                        <i class="fas fa-box me-2"></i> Item <?= $idx + 1 ?> -
                                                                        <?= htmlspecialchars($detail['name'] ?? '-') ?>
                                                                    </button>
                                                                </h2>
                                                                <div id="collapse<?= $collapseId ?>" class="accordion-collapse collapse"
                                                                    aria-labelledby="heading<?= $collapseId ?>"
                                                                    data-bs-parent="#accordionShipment">
                                                                    <div class="accordion-body p-2">
                                                                        <div class="table-responsive">
                                                                            <table class="table table-sm table-borderless mb-0 w-100">
                                                                                <?php foreach ($detail as $dKey => $dVal): ?>
                                                                                    <tr>
                                                                                        <th style="width:150px;"><?= ucwords(str_replace('_', ' ', $dKey)) ?></th>
                                                                                        <td><?= htmlspecialchars($dVal) ?></td>
                                                                                    </tr>
                                                                                <?php endforeach; ?>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <th style="width:180px;"><?= ucwords(str_replace('_', ' ', $key)) ?></th>
                                                <td><?= htmlspecialchars($value) ?></td>
                                            </tr>
                                        <?php endif;
                                    endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="2"><?= htmlspecialchars($order['data']) ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Card: Shipment Images -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-info text-white">
                        <div><i class="fas fa-image me-2"></i> Shipment Images</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <?php
                            $images = $this->db->get_where('shipment_images', ['order_id' => $order['id']])->result_array();
                            if (!empty($images)):
                                foreach ($images as $img):
                                    $img_url = base_url(ltrim($img['file_path'], '/'));
                                    $file_path = FCPATH . ltrim($img['file_path'], '/');
                                    if (is_file($file_path)): ?>
                                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                            <img src="<?= htmlspecialchars($img_url) ?>" class="img-thumbnail w-100"
                                                style="max-height:150px; object-fit:cover;" alt="Shipment Image">
                                        </div>
                                    <?php endif;
                                endforeach;
                            else: ?>
                                <div class="col">
                                    <span class="text-muted">No images available.</span><br>
                                    <a href="<?= site_url('order/upload_form/' . $order['id']) ?>"
                                        class="btn btn-sm btn-primary mt-2">
                                        <i class="fas fa-upload"></i> Upload Ulang
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="alert alert-warning">Data order tidak ditemukan.</div>
            <?php endif; ?>
        </div>
    </main>
    <?php $this->load->view('layout/footer'); ?>
</div>
