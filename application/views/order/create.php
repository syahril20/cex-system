<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4 mb-4">Create New Order</h1>

            <form action="<?= base_url('order/do_create') ?>" method="post">

                <!-- Card: Shipper -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-user me-2"></i> Shipper
                    </div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Shipper Name</label>
                            <input type="text" name="ship_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Shipper Phone</label>
                            <input type="text" name="ship_phone" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Shipper Address</label>
                            <textarea name="ship_address" class="form-control" rows="2" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Card: Receiver -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <i class="fas fa-user-friends me-2"></i> Receiver
                    </div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Receiver Name</label>
                            <input type="text" name="rec_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Receiver Phone</label>
                            <input type="text" name="rec_phone" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Receiver Address</label>
                            <textarea name="rec_address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Postcode</label>
                            <input type="text" name="rec_postcode" value="00000" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">City</label>
                            <input type="text" name="rec_city" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Country</label>
                            <input type="text" name="rec_country" value="United Arab Emirates" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Country Code</label>
                            <input type="text" name="rec_country_code" value="AE" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Card: Shipment -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-truck me-2"></i> Shipment Data
                    </div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Weight (kg)</label>
                            <input type="number" name="berat" value="1" step="0.01" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">ARC No</label>
                            <input type="text" name="arc_no" value="-" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Total Qty</label>
                            <input type="number" name="total_qty" value="1" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Total Value</label>
                            <input type="number" name="total_value" value="10" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Goods Category</label>
                            <input type="number" name="goods_category" value="1" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Goods Description</label>
                            <input type="text" name="goods_description" value="Jacket" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea name="notes" class="form-control">Its a notes</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Service Type</label>
                            <select name="service_type" class="form-select" required>
                                <option value="">-- Select Service Type --</option>
                                <?php if (!empty($rates)): ?>
                                    <?php foreach ($rates as $rate): ?>
                                        <option value="<?= htmlspecialchars($rate['id']) ?>">
                                            <?= htmlspecialchars($rate['text']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Height</label>
                            <input type="number" name="height" value="10" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Width</label>
                            <input type="number" name="width" value="15.5" step="0.1" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Length</label>
                            <input type="number" name="length" value="10.5" step="0.1" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Is Connote Reff</label>
                            <select name="is_connote_reff" class="form-select">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Connote Reff</label>
                            <input type="text" name="connote_reff" value="-" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Card: Shipment Details -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-cubes me-2"></i> Shipment Details
                    </div>
                    <div class="card-body">
                        <div class="mb-3 d-flex gap-2">
                            <button type="button" class="btn btn-success btn-sm" id="add-shipment-item">
                                <i class="fas fa-plus"></i> Tambah Item
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" id="remove-shipment-item">
                                <i class="fas fa-minus"></i> Hapus Item
                            </button>
                        </div>
                        <div id="shipment-details-container">
                            <div class="shipment-detail-group col-md-12 mb-3 p-3 border rounded bg-light">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Item Name</label>
                                        <input type="text" name="shipment_details[0][name]" value="Jacket"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Category</label>
                                        <select name="shipment_details[0][category]" class="form-select" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            <?php if (!empty($commodities)): ?>
                                                <?php foreach ($commodities as $commodity): ?>
                                                    <option value="<?= htmlspecialchars($commodity['text']) ?>">
                                                        <?= htmlspecialchars($commodity['text']) ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Qty</label>
                                        <input type="number" name="shipment_details[0][qty]" value="1"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Price</label>
                                        <input type="number" name="shipment_details[0][price]" value="20"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-save"></i> Submit Order
                    </button>
                    <a href="<?= base_url('order') ?>" class="btn btn-secondary flex-fill">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>

            </form>
        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>

    <script>
        let shipmentIndex = 1;
        document.getElementById('add-shipment-item').addEventListener('click', function () {
            const container = document.getElementById('shipment-details-container');
            const group = document.createElement('div');
            group.className = 'shipment-detail-group col-md-12 mb-3 p-3 border rounded bg-light';
            group.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Item Name</label>
                        <input type="text" name="shipment_details[${shipmentIndex}][name]" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Category</label>
                        <select name="shipment_details[${shipmentIndex}][category]" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php if (!empty($commodities)): ?>
                                <?php foreach ($commodities as $commodity): ?>
                                    <option value="<?= htmlspecialchars($commodity['text']) ?>"><?= htmlspecialchars($commodity['text']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Qty</label>
                        <input type="number" name="shipment_details[${shipmentIndex}][qty]" value="1" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Price</label>
                        <input type="number" name="shipment_details[${shipmentIndex}][price]" value="0" class="form-control" required>
                    </div>
                </div>
            `;
            container.appendChild(group);
            shipmentIndex++;
        });

        document.getElementById('remove-shipment-item').addEventListener('click', function () {
            const container = document.getElementById('shipment-details-container');
            const groups = container.getElementsByClassName('shipment-detail-group');
            if (groups.length > 1) {
                container.removeChild(groups[groups.length - 1]);
                shipmentIndex--;
            }
        });
    </script>
</div>