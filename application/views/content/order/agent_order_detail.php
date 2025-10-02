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
            <h1 class="mt-4">Detail Order</h1>

            <?php
            $id_order = $this->uri->segment(3);
            $query = $this->db->get_where('orders', ['id' => $id_order]);
            $order = $query->row_array();
            ?>

            <?php if ($order): ?>
                <!-- Card: Informasi Order -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-info-circle me-2"></i> Informasi Order
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-sm table-striped mb-0">

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
                                    if ($status == 'complete') {
                                        $badgeClass = 'bg-success';
                                    } elseif ($status == 'created') {
                                        $badgeClass = 'bg-primary';
                                    } elseif ($status == 'cancelled') {
                                        $badgeClass = 'bg-danger';
                                    } elseif ($status == 'rejected') {
                                        $badgeClass = 'bg-danger';
                                    } else {
                                        // Status perjalanan atau sedang berada di lokasi tertentu
                                        $badgeClass = 'bg-info text-dark';
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?> text-wrap text-start"
                                        style="white-space: normal; word-break: break-word; max-width: 350x; display: inline-block; line-height:1.2;">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td><?= htmlspecialchars($order['created_at']) ?></td>
                            </tr>

                            <tr>
                                <th>Created By</th>
                                <td><?= htmlspecialchars($order['created_by']) ?></td>
                            </tr>

                        </table>
                    </div>
                </div>

                <!-- Card: Data Pengiriman -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <i class="fas fa-database me-2"></i> Data Pengiriman
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-hover table-sm mb-0">
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
                                                                    <table class="table table-sm table-borderless mb-0">
                                                                        <?php foreach ($detail as $dKey => $dVal): ?>
                                                                            <tr>
                                                                                <th style="width:150px;">
                                                                                    <?= ucwords(str_replace('_', ' ', $dKey)) ?>
                                                                                </th>
                                                                                <td><?= htmlspecialchars($dVal) ?></td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </table>
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

                <!-- Card: Shipment Images -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-image me-2"></i> Shipment Images
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            $images = $this->db->get_where('shipment_images', ['order_id' => $order['id']])->result_array();
                            $hasValidImage = false;
                            if (!empty($images)):
                                foreach ($images as $img):
                                    $img_url = base_url(ltrim($img['file_path'], '/'));
                                    $file_path = FCPATH . ltrim($img['file_path'], '/');
                                    if (is_file($file_path)):
                                        $hasValidImage = true; ?>
                                        <div class="col-auto">
                                            <img src="<?= htmlspecialchars($img_url) ?>" class="img-thumbnail"
                                                style="max-width:120px; max-height:120px;" alt="Shipment Image">
                                        </div>
                                        <?php
                                    endif;
                                endforeach;
                            endif;

                            if (!$hasValidImage): ?>
                                <div class="col">
                                    <span class="text-muted">No images available.</span><br>
                                    <?php if (strtolower($order['status']) === 'rejected'): ?>
                                        <button class="btn btn-sm btn-primary mt-2" disabled>
                                            <i class="fas fa-upload"></i> Upload Ulang
                                        </button>
                                    <?php else: ?>
                                        <a href="<?= site_url('order/upload_form/' . $order['id']) ?>"
                                            class="btn btn-sm btn-primary mt-2">
                                            <i class="fas fa-upload"></i> Upload Ulang
                                        </a>
                                    <?php endif; ?>
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