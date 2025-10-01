<link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>">


<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Data Order</h1>

            <div class="card mb-4">
                <div class="card-body">
                    <form method="post" action="<?= site_url('order/do_create'); ?>" class="row g-3">
                        <!-- Shipper -->
                        <div class="col-md-6">
                            <label class="form-label">Shipper Name</label>
                            <input type="text" name="ship_name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Shipper Address</label>
                            <textarea name="ship_address" class="form-control" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Shipper Phone</label>
                            <input type="text" name="ship_phone" class="form-control" required>
                        </div>

                        <!-- Receiver -->
                        <div class="col-md-6">
                            <label class="form-label">Receiver Name</label>
                            <input type="text" name="rec_name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Receiver Address</label>
                            <textarea name="rec_address" class="form-control" required></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Receiver Postcode</label>
                            <input type="text" name="rec_postcode" value="00000" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Receiver City</label>
                            <input type="text" name="rec_city" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Receiver Phone</label>
                            <input type="text" name="rec_phone" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Receiver Country</label>
                            <input type="text" name="rec_country" value="United Arab Emirates" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Receiver Country Code</label>
                            <input type="text" name="rec_country_code" value="AE" class="form-control">
                        </div>

                        <!-- Shipment -->
                        <div class="col-md-4">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" name="berat" value="1" step="0.01" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">ARC No</label>
                            <input type="text" name="arc_no" value="-" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Total Quantity</label>
                            <input type="number" name="total_qty" value="1" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Total Value</label>
                            <input type="number" name="total_value" value="10" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Goods Category</label>
                            <input type="number" name="goods_category" value="1" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Goods Description</label>
                            <input type="text" name="goods_description" value="Jacket" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control">Its a notes</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Service Type</label>
                            <select name="service_type" class="form-select" required>
                                <option value="">-- Pilih Service Type --</option>
                                <?php if (!empty($rates) && is_array($rates)): ?>
                                    <?php foreach ($rates as $rate): ?>
                                        <option value="<?= htmlspecialchars($rate['id']) ?>">
                                            <?= htmlspecialchars($rate['text']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Height</label>
                            <input type="number" name="height" value="10" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Width</label>
                            <input type="number" name="width" value="15.5" step="0.1" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Length</label>
                            <input type="number" name="length" value="10.5" step="0.1" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Is Connote Reff</label>
                            <select name="is_connote_reff" class="form-select">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Connote Reff</label>
                            <input type="text" name="connote_reff" value="-" class="form-control">
                        </div>

                        <!-- Shipment Details -->
                        <div id="shipment-details-container" class="row">
                            <div class="shipment-detail-group col-md-12 mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Shipment Item Name</label>
                                        <input type="text" name="shipment_details[0][name]" value="Jacket"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Shipment Item Category</label>
                                        <select name="shipment_details[0][category]" class="form-select" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            <?php if (!empty($commodities) && is_array($commodities)): ?>
                                                <?php foreach ($commodities as $commodity): ?>
                                                    <option value="<?= htmlspecialchars($commodity['text']) ?>">
                                                        <?= htmlspecialchars($commodity['text']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Shipment Item Qty</label>
                                        <input type="number" name="shipment_details[0][qty]" value="1"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Shipment Item Price</label>
                                        <input type="number" name="shipment_details[0][price]" value="20"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <button type="button" class="btn btn-success" id="add-shipment-item">Tambah Item</button>
                            <button type="button" class="btn btn-danger" id="remove-shipment-item">Hapus Item</button>
                        </div>
                        <script>
                            let shipmentIndex = 1;
                            document.getElementById('add-shipment-item').addEventListener('click', function () {
                                const container = document.getElementById('shipment-details-container');
                                const group = document.createElement('div');
                                group.className = 'shipment-detail-group col-md-12 mb-3';
                                group.innerHTML = `
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Shipment Item Name</label>
                                            <input type="text" name="shipment_details[${shipmentIndex}][name]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Shipment Item Category</label>
                                            <select name="shipment_details[${shipmentIndex}][category]" class="form-select" required>
                                                <option value="">-- Pilih Kategori --</option>
                                                <?php if (!empty($commodities) && is_array($commodities)): ?>
                                                    <?php foreach ($commodities as $commodity): ?>
                                                        <option value="<?= htmlspecialchars($commodity['text']) ?>">
                                                            <?= htmlspecialchars($commodity['text']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Shipment Item Qty</label>
                                            <input type="number" name="shipment_details[${shipmentIndex}][qty]" value="1" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Shipment Item Price</label>
                                            <input type="number" name="shipment_details[${shipmentIndex}][price]" value="0" class="form-control" required>
                                        </div>
                                    </div>
                                `;
                                container.appendChild(group);
                                shipmentIndex++;
                            });

                            document.getElementById('remove-shipment-item').addEventListener('click', function () {
                                const container = document.getElementById('shipment-details-container');
                                // Only remove if more than one group exists
                                const groups = container.getElementsByClassName('shipment-detail-group');
                                if (groups.length > 1) {
                                    container.removeChild(groups[groups.length - 1]);
                                    shipmentIndex--;
                                }
                            });
                        </script>
                        <div class="col-12"></div>
                        <button type="submit" class="btn btn-primary">Submit Order</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php $this->load->view('layout/footer'); ?>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/js/datatables-simple-demo.js') ?>"></script>
</div>