<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4 mb-4">Edit Order</h1>

            <form action="<?= base_url('order/do_edit/' . $data['order']->id) ?>" method="post">

                <!-- Card: Informasi Order -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-info-circle me-2"></i> Informasi Order
                    </div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Airwaybill</label>
                            <input type="text" class="form-control bg-light"
                                value="<?= htmlspecialchars($data['order']->airwaybill) ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <input type="text" class="form-control bg-light"
                                value="<?= htmlspecialchars($data['order']->status) ?>" disabled>
                        </div>
                    </div>
                </div>

                <!-- Card: Shipment Data -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <i class="fas fa-box me-2"></i> Shipment Data
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php
                            $order_data = $data['order_data'] ?? [];
                            $jsonFields = [
                                'ship_name','ship_address','ship_phone',
                                'rec_name','rec_address','rec_postcode','rec_city','rec_phone',
                                'rec_country','rec_country_code','berat','arc_no','total_qty','total_value',
                                'goods_category','goods_description','notes','height','width','length',
                                'is_connote_reff','connote_reff'
                            ];
                            foreach ($jsonFields as $field): ?>
                                <div class="col-md-6">
                                    <label class="form-label"><?= ucwords(str_replace('_',' ',$field)) ?></label>
                                    <input type="text" class="form-control"
                                           name="data[<?= $field ?>]"
                                           value="<?= htmlspecialchars($order_data[$field] ?? '') ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Card: Service Type -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-cogs me-2"></i> Service Type
                    </div>
                    <div class="card-body">
                        <select class="form-select" name="data[service_type]">
                            <option value="">-- Select Service Type --</option>
                            <?php foreach($data['rates'] as $rate): ?>
                                <option value="<?= htmlspecialchars($rate['id']) ?>"
                                    <?= ($order_data['service_type'] ?? '') == $rate['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($rate['text']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Card: Shipment Details -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
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
                    <a href="<?= base_url('order') ?>" class="btn btn-secondary flex-fill">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <?php $this->load->view('layout/footer'); ?>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let shipmentDetails = <?= json_encode($data['order_data']['shipment_details'] ?? []) ?>;
        const commodities = <?= json_encode($data['commodities'] ?? []) ?>;

        function renderShipmentDetails() {
            const container = document.getElementById('shipmentDetailsContainer');
            container.innerHTML = '';
            shipmentDetails.forEach((item, index) => {
                let categoryOptions = '<option value="">-- Select Category --</option>';
                commodities.forEach(c => {
                    const selected = (item.category === c.text) ? 'selected' : '';
                    categoryOptions += `<option value="${c.text}" ${selected}>${c.text}</option>`;
                });

                container.innerHTML += `
                    <div class="shipment-item mb-3 p-3 border rounded shadow-sm bg-light position-relative">
                        <span class="remove-item text-danger fw-bold"
                              style="cursor:pointer;position:absolute;top:10px;right:10px;font-size:1.2rem;"
                              onclick="removeShipmentDetail(${index})">&times;</span>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control"
                                       name="data[shipment_details][${index}][name]"
                                       value="${item.name ?? ''}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="data[shipment_details][${index}][category]">
                                    ${categoryOptions}
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control"
                                       name="data[shipment_details][${index}][qty]"
                                       value="${item.qty ?? '1'}" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Price</label>
                                <input type="number" class="form-control"
                                       name="data[shipment_details][${index}][price]"
                                       value="${item.price ?? '0'}" min="0" required>
                            </div>
                        </div>
                    </div>`;
            });
        }

        function addShipmentDetail() {
            shipmentDetails.push({name:'',category:'',qty:'1',price:'0'});
            renderShipmentDetails();
        }

        function removeShipmentDetail(index) {
            shipmentDetails.splice(index,1);
            renderShipmentDetails();
        }

        function removeLastShipmentDetail() {
            if(shipmentDetails.length > 0) {
                shipmentDetails.pop();
                renderShipmentDetails();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderShipmentDetails();
            const form = document.querySelector('form[action*="do_edit"]');
            const btn = document.getElementById('btn-update-order');

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Update this order data?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!'
                }).then(result => {
                    if(result.isConfirmed) form.submit();
                });
            });
        });
    </script>
</div>
