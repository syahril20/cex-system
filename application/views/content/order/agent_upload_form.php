<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Upload Shipment Image</h1>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-body">
                    <form method="post" action="<?= site_url('order/do_uploads'); ?>" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Airwaybill</label>
                            <input type="text" name="airwaybill" class="form-control" placeholder="Masukkan Airwaybill" 
                            value="<?php
                                // Ambil id dari segment 3
                                $order_id = $this->uri->segment(3);
                                // Query ke database untuk dapatkan airwaybill
                                $this->db->where('id', $order_id);
                                $order = $this->db->get('orders')->row();
                                echo isset($order->airwaybill) ? $order->airwaybill : '';
                            ?>" required>
                        </div>

                        <input type="hidden" name="order_id" value="<?php echo $this->uri->segment(3); ?>">

                        <div class="mb-3">
                            <label class="form-label">Pilih File</label>
                            <input type="file" name="filename" class="form-control" accept="image/*" required onchange="previewImage(event)">
                        </div>

                        <div class="mb-3" id="imagePreviewContainer" style="display:none;">
                            <label class="form-label">Preview Gambar</label>
                            <br>
                            <img id="imagePreview" src="#" alt="Preview" style="max-width: 300px; max-height: 300px; border:1px solid #ddd; padding:5px;">
                        </div>

                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>

                    <script>
                    function previewImage(event) {
                        var input = event.target;
                        var preview = document.getElementById('imagePreview');
                        var container = document.getElementById('imagePreviewContainer');
                        if (input.files && input.files[0]) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                container.style.display = 'block';
                            }
                            reader.readAsDataURL(input.files[0]);
                        } else {
                            preview.src = '#';
                            container.style.display = 'none';
                        }
                    }
                    </script>
                </div>
            </div>

        </div>
    </main>
    <?php $this->load->view('layout/footer'); ?>
</div>