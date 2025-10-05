<script src="<?= base_url('assets/js/sweetalert2@11.js') ?>"></script>
<script>
    let shipmentDetails = <?= json_encode($order_data['shipment_details'] ?? []) ?>;
    const commodities = <?= json_encode($commodities ?? []) ?>;

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
                    <div class="shipment-item mb-3 p-3 border rounded bg-light shadow-sm position-relative">
                        <span class="remove-item text-danger fw-bold"
                              style="cursor:pointer;position:absolute;top:10px;right:10px;font-size:1.2rem;"
                              onclick="removeShipmentDetail(${index})">&times;</span>
                        <h6 class="fw-bold text-muted mb-3">Item #${index + 1}</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Name</label>
                                <input type="text" class="form-control"
                                       name="data[shipment_details][${index}][name]"
                                       value="${item.name ?? ''}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Category</label>
                                <select class="form-select" name="data[shipment_details][${index}][category]">
                                    ${categoryOptions}
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Qty</label>
                                <input type="number" class="form-control"
                                       name="data[shipment_details][${index}][qty]"
                                       value="${item.qty ?? '1'}" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Price</label>
                                <input type="number" class="form-control"
                                       name="data[shipment_details][${index}][price]"
                                       value="${item.price ?? '0'}" min="0" required>
                            </div>
                        </div>
                    </div>`;
        });
    }

    function addShipmentDetail() {
        shipmentDetails.push({ name: '', category: '', qty: '1', price: '0' });
        renderShipmentDetails();
    }

    function removeShipmentDetail(index) {
        shipmentDetails.splice(index, 1);
        renderShipmentDetails();
    }

    function removeLastShipmentDetail() {
        if (shipmentDetails.length > 0) {
            shipmentDetails.pop();
            renderShipmentDetails();
        }
    }
</script>