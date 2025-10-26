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
                            <input type="text" name="ship_phone" class="form-control" required pattern="[0-9]+"
                                title="Only numbers are allowed" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Shipper Address</label>
                            <textarea name="ship_address" class="form-control" rows="2" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- =================== Card: Receiver =================== -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-user-friends me-2"></i> Receiver
                        </div>
                        <div>
                            <select id="receiverSelect" class="form-select form-select-sm" style="width: 240px;">
                                <option value="">-- Select Saved Receiver --</option>
                                <?php foreach ($receivers as $r): ?>
                                    <option value="<?= htmlspecialchars($r['id']) ?>"
                                        data-name="<?= htmlspecialchars($r['name']) ?>"
                                        data-phone="<?= htmlspecialchars($r['phone']) ?>"
                                        data-address="<?= htmlspecialchars($r['address']) ?>"
                                        data-postcode="<?= htmlspecialchars($r['postal_code']) ?>"
                                        data-city="<?= htmlspecialchars($r['city']) ?>"
                                        data-countryid="<?= htmlspecialchars(trim((string) $r['id_country'])) ?>">
                                        <?= htmlspecialchars($r['name']) ?> - <?= htmlspecialchars($r['city']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Receiver Name</label>
                            <input type="text" name="rec_name" id="rec_name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Receiver Phone</label>
                            <input type="text" name="rec_phone" id="rec_phone" class="form-control" pattern="[0-9]+"
                                title="Only numbers are allowed" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Receiver Address</label>
                            <textarea name="rec_address" id="rec_address" class="form-control" rows="2"
                                required></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Postcode</label>
                            <input type="text" name="rec_postcode" id="rec_postcode" class="form-control" required
                                pattern="[0-9]+" title="Only numbers are allowed" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">City</label>
                            <input type="text" name="rec_city" id="rec_city" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Country</label>
                            <select id="rec_country" class="form-control" required>
                                <option value="">-- Select Country --</option>
                                <?php foreach ($country_data as $country): ?>
                                    <option value="<?= htmlspecialchars(trim((string) $country['id_country'])) ?>"
                                        data-name="<?= htmlspecialchars($country['country_name']) ?>">
                                        <?= htmlspecialchars($country['country_name']) ?>
                                        (<?= htmlspecialchars($country['code2']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- hidden input untuk kirim nama -->
                            <input type="hidden" name="rec_country" id="rec_country_name">
                            <input type="hidden" id="rec_country_code" name="rec_country_code">
                            <?php foreach ($country_data as $country): ?>
                                <input type="hidden" class="country-code-map"
                                    data-id="<?= htmlspecialchars(trim((string) $country['id_country'])) ?>"
                                    data-code="<?= htmlspecialchars($country['code2']) ?>">
                            <?php endforeach; ?>

                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const countrySelect = document.getElementById('rec_country');
                                    const hiddenCode = document.getElementById('rec_country_code');

                                    function updateCountryCode() {
                                        const val = (countrySelect.value || '').trim();
                                        const map = document.querySelector('.country-code-map[data-id="' + val + '"]');
                                        hiddenCode.value = map ? map.dataset.code : '';
                                        console.log('Country code set to:', hiddenCode.value);
                                    }

                                    countrySelect.addEventListener('change', updateCountryCode);

                                    // Ensure code is set on form submit as well
                                    const form = document.querySelector('form[action*="order/do_create"]');
                                    if (form) {
                                        form.addEventListener('submit', updateCountryCode);
                                    }

                                    // Initialize if a country is already selected
                                    updateCountryCode();
                                });
                            </script>
                        </div>
                        <div class="col-12 text-end mt-3">
                            <button type="button" id="saveReceiverBtn" class="btn btn-success me-2">
                                <i class="fas fa-save me-1"></i> Save Receiver
                            </button>
                            <button type="button" id="deleteReceiverBtn" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Delete Receiver
                            </button>
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
                            <input type="number" name="berat" value="1" step="0.01" class="form-control" required
                                pattern="[0-9]+([.][0-9]+)?" title="Only numbers are allowed" inputmode="decimal"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '')">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">ARC No</label>
                            <input type="text" name="arc_no" value="-" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Total Qty</label>
                            <input type="number" name="total_qty" value="1" class="form-control" required
                                pattern="[0-9]+" title="Only numbers are allowed" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Total Value</label>
                            <input type="number" name="total_value" value="10" class="form-control" required
                                pattern="[0-9]+([.][0-9]+)?" title="Only numbers are allowed" inputmode="decimal"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '')">
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
                                            <?= htmlspecialchars($rate['text']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Height</label>
                            <input type="number" name="height" value="15.5" step="0.1" class="form-control">
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
                                                        <?= htmlspecialchars($commodity['text']) ?>
                                                    </option>
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


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[action*="order/do_create"]');
            const countrySelect = document.getElementById('rec_country');
            const hiddenCountryName = document.getElementById('rec_country_name');

            form.addEventListener('submit', function () {
                const selected = countrySelect.options[countrySelect.selectedIndex];
                hiddenCountryName.value = selected ? selected.dataset.name : '';
                console.log("Submit =>", hiddenCountryName.value);
            });
        });

    </script>

    <!-- ============== Script Autofill Receiver (Universal Plugin Support) ============== -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const receiverSelect = document.getElementById('receiverSelect');
            const countrySelect = document.getElementById('rec_country');

            const nameInput = document.getElementById('rec_name');
            const phoneInput = document.getElementById('rec_phone');
            const addressInput = document.getElementById('rec_address');
            const postcodeInput = document.getElementById('rec_postcode');
            const cityInput = document.getElementById('rec_city');

            receiverSelect.addEventListener('change', function () {
                const option = this.options[this.selectedIndex];

                if (!option.value) {
                    nameInput.value = '';
                    phoneInput.value = '';
                    addressInput.value = '';
                    postcodeInput.value = '';
                    cityInput.value = '';
                    countrySelect.value = '';
                    triggerCountryUI('');
                    return;
                }

                // Isi data form
                nameInput.value = option.dataset.name || '';
                phoneInput.value = option.dataset.phone || '';
                addressInput.value = option.dataset.address || '';
                postcodeInput.value = option.dataset.postcode || '';
                cityInput.value = option.dataset.city || '';

                // Tentukan country id target
                const targetCountryId = (option.dataset.countryid || '').trim();
                console.log("Trying to set country:", targetCountryId);

                // Set value
                countrySelect.value = targetCountryId;
                if (countrySelect.value !== targetCountryId) {
                    for (const opt of countrySelect.options) {
                        if (opt.value.trim() === targetCountryId) {
                            opt.selected = true;
                            break;
                        }
                    }
                }

                // Update tampilan plugin (Select2, Bootstrap Select, atau Choices.js)
                triggerCountryUI(targetCountryId);

                console.log("Final country value:", countrySelect.value);
            });

            /**
             * Fungsi untuk memaksa UI dropdown country update
             * Deteksi otomatis library yang digunakan
             */
            function triggerCountryUI(value) {
                // Jika pakai Select2
                if (typeof $ !== 'undefined' && $('#rec_country').data('select2')) {
                    $('#rec_country').val(value).trigger('change.select2');
                    console.log('✅ Select2 country UI updated');
                    return;
                }

                // Jika pakai Bootstrap Select
                if (typeof $ !== 'undefined' && $('#rec_country').data('selectpicker')) {
                    $('#rec_country').val(value).selectpicker('refresh');
                    console.log('✅ Bootstrap Select country UI updated');
                    return;
                }

                // Jika pakai Choices.js
                if (countrySelect.choices) {
                    countrySelect.choices.setChoiceByValue(value);
                    console.log('✅ Choices.js country UI updated');
                    return;
                }

                // Default native select
                countrySelect.value = value;
                console.log('✅ Native country select updated');
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const receiverSelect = document.getElementById('receiverSelect');
            const saveBtn = document.getElementById('saveReceiverBtn');
            const deleteBtn = document.getElementById('deleteReceiverBtn');

            // === SAVE RECEIVER ===
            saveBtn.addEventListener('click', function () {
                const data = {
                    name: document.getElementById('rec_name').value.trim(),
                    phone: document.getElementById('rec_phone').value.trim(),
                    address: document.getElementById('rec_address').value.trim(),
                    city: document.getElementById('rec_city').value.trim(),
                    postal_code: document.getElementById('rec_postcode').value.trim(),
                    id_country: document.getElementById('rec_country').value.trim()
                };

                if (!data.name || !data.phone || !data.address || !data.city || !data.id_country) {
                    Swal.fire('Oops!', 'Please fill all required fields!', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Save Receiver?',
                    text: "Data receiver akan disimpan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, save it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch('<?= base_url('receiver/save') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(data)
                        })
                            .then(res => res.json())
                            .then(res => {
                                if (res.status === 'duplicate') {
                                    Swal.fire('Duplicate!', 'Receiver already exists!', 'error');
                                } else if (res.status === 'success') {
                                    Swal.fire('Saved!', 'Receiver saved successfully.', 'success');
                                    addReceiverToDropdown(res.data);
                                } else {
                                    Swal.fire('Failed!', 'Unable to save receiver.', 'error');
                                }
                            })
                            .catch(() => Swal.fire('Error!', 'Request failed.', 'error'));
                    }
                });
            });

            // === DELETE RECEIVER ===
            deleteBtn.addEventListener('click', function () {
                const selectedId = receiverSelect.value;
                if (!selectedId) {
                    Swal.fire('Oops!', 'Please select a receiver to delete!', 'info');
                    return;
                }

                Swal.fire({
                    title: 'Delete Receiver?',
                    text: "Data receiver akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch('<?= base_url('receiver/delete/') ?>' + selectedId, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(res => res.json())
                            .then(res => {
                                if (res.status === 'success') {
                                    Swal.fire('Deleted!', 'Receiver deleted successfully.', 'success');
                                    removeReceiverFromDropdown(selectedId);
                                } else {
                                    Swal.fire('Failed!', 'Unable to delete receiver.', 'error');
                                }
                            })
                            .catch(() => Swal.fire('Error!', 'Delete failed.', 'error'));
                    }
                });
            });

            // Helper menambah receiver baru ke dropdown
            function addReceiverToDropdown(receiver) {
                const option = document.createElement('option');
                option.value = receiver.id;
                option.dataset.name = receiver.name;
                option.dataset.phone = receiver.phone;
                option.dataset.address = receiver.address;
                option.dataset.postcode = receiver.postal_code;
                option.dataset.city = receiver.city;
                option.dataset.countryid = receiver.id_country;
                option.textContent = `${receiver.name} - ${receiver.city}`;
                receiverSelect.appendChild(option);
                receiverSelect.value = receiver.id;
                receiverSelect.dispatchEvent(new Event('change'));
            }

            // Helper hapus receiver dari dropdown
            function removeReceiverFromDropdown(id) {
                const opt = receiverSelect.querySelector(`option[value="${id}"]`);
                if (opt) opt.remove();
                receiverSelect.value = '';
                receiverSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>

    <?php $this->load->view('layout/footer'); ?>
    <?php $this->load->view('components/order_create_script'); ?>
    <script src="<?= base_url('assets/js/sweetalert2@11.js') ?>"></script>

</div>