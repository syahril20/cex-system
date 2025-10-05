<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4 mb-4">
                <i class="fas fa-edit text-primary me-2"></i> Edit Order
            </h1>

            <form action="<?= base_url('order/do_edit/' . $order->id) ?>" method="post">

                <!-- Card: Informasi Order -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-primary text-white fw-semibold">
                        <i class="fas fa-info-circle me-2"></i> Informasi Order
                    </div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Airwaybill</label>
                            <input type="text" class="form-control bg-light"
                                value="<?= htmlspecialchars($order->airwaybill) ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Status</label>
                            <input type="text" class="form-control bg-light"
                                value="<?= htmlspecialchars($order->status) ?>" disabled>
                        </div>
                    </div>
                </div>

                <!-- Card: Shipment Data -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-secondary text-white fw-semibold">
                        <i class="fas fa-box me-2"></i> Shipment Data
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php
                            $order_data = $order_data ?? [];
                            $jsonFields = [
                                'ship_name',
                                'ship_address',
                                'ship_phone',
                                'rec_name',
                                'rec_address',
                                'rec_postcode',
                                'rec_city',
                                'rec_phone',
                                'rec_country',
                                'rec_country_code',
                                'berat',
                                'arc_no',
                                'total_qty',
                                'total_value',
                                'goods_category',
                                'goods_description',
                                'notes',
                                'height',
                                'width',
                                'length',
                                'is_connote_reff',
                                'connote_reff'
                            ];
                            foreach ($jsonFields as $field): ?>
                                <div class="col-md-6">
                                    <label
                                        class="form-label fw-semibold small"><?= ucwords(str_replace('_', ' ', $field)) ?></label>
                                    <input type="text" class="form-control" name="data[<?= $field ?>]"
                                        value="<?= htmlspecialchars($order_data[$field] ?? '') ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Card: Service Type -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-info text-white fw-semibold">
                        <i class="fas fa-cogs me-2"></i> Service Type
                    </div>
                    <div class="card-body">
                        <select class="form-select" name="data[service_type]">
                            <option value="">-- Select Service Type --</option>
                            <?php foreach ($rates as $rate): ?>
                                <option value="<?= htmlspecialchars($rate['id']) ?>" <?= ($order_data['service_type'] ?? '') == $rate['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($rate['text']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Card: Shipment Details -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-warning text-dark fw-semibold">
                        <i class="fas fa-list me-2"></i> Shipment Details
                    </div>
                    <div class="card-body">
                        <div class="mb-3 d-flex gap-2">
                            <button type="button" class="btn btn-success btn-sm" onclick="addShipmentDetail()">
                                <i class="fas fa-plus"></i> Tambah Item
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeLastShipmentDetail()">
                                <i class="fas fa-minus"></i> Kurangi Item
                            </button>
                        </div>
                        <div id="shipmentDetailsContainer"></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary flex-fill" id="btn-update-order">
                        <i class="fas fa-save"></i> Update Order
                    </button>
                    <a href="<?= base_url('order') ?>" class="btn btn-outline-secondary flex-fill">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>
    <?php $this->load->view('components/order_edit_script'); ?>
    <?php $this->load->view('components/order_edit_modal'); ?>
</div>