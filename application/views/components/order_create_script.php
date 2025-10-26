<link href="<?= site_url('assets/css/select2.min.css') ?>" rel="stylesheet" />
<script src="<?= site_url('assets/js/jquery-3.6.0.min.js') ?>"></script>
<script src="<?= site_url('assets/js/select2.min.js') ?>"></script>

<script>
    $(document).ready(function () {
        $('#rec_country').select2({
            placeholder: 'Search Country...',
            width: '100%'
        });

        // Samakan tinggi elemen Select2 dengan input lain (inline style)
        $('.select2-selection--single').attr('style',
            'height: calc(2.4rem) !important; padding: 0.375rem 0.75rem !important; border: 1px solid #ced4da !important; border-radius: 0.375rem !important; display: flex; align-items: center;'
        );
        $('.select2-selection__rendered').attr('style',
            'line-height: 2.5rem !important; font-size: 1rem !important;'
        );

        // Update kode negara
        $('#rec_country').on('change', function () {
            let code = $(this).find(':selected').data('code');
            $('#rec_country_code').val(code || '');
        });
    });

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
                        <input type="text" name="shipment_details[${shipmentIndex}][qty]" value="1" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Price</label>
                        <input type="text" name="shipment_details[${shipmentIndex}][price]" value="0" class="form-control" required>
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