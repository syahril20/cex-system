<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4 mb-4">Upload Shipment Image</h1>

            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= $this->session->flashdata('error'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= $this->session->flashdata('success'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Upload Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-upload me-2"></i> Form Upload Gambar
                </div>
                <div class="card-body">
                    <form method="post" action="<?= site_url('order/do_uploads'); ?>" enctype="multipart/form-data">

                        <!-- Airwaybill -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Airwaybill</label>
                            <input type="text" name="airwaybill" class="form-control" placeholder="Masukkan Airwaybill"
                                value="<?php
                                $order_id = $this->uri->segment(3);
                                $this->db->where('id', $order_id);
                                $order = $this->db->get('orders')->row();
                                echo isset($order->airwaybill) ? $order->airwaybill : '';
                                ?>" required>
                        </div>

                        <input type="hidden" name="order_id" value="<?php echo $this->uri->segment(3); ?>">

                        <!-- File Input -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih File</label>
                            <input type="file" name="filename" class="form-control" accept="image/*" required
                                onchange="previewImage(event)">
                        </div>

                        <!-- Preview -->
                        <div class="mb-3" id="imagePreviewContainer" style="display:none;">
                            <label class="form-label fw-bold">Preview Gambar</label>
                            <div class="border rounded p-2 bg-light text-center">
                                <img id="imagePreview" src="#" alt="Preview"
                                    style="max-width: 100%; max-height: 300px; object-fit: contain;">
                            </div>
                        </div>

                        <!-- Action -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload
                            </button>
                            <a href="<?= site_url('order') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>
    <?php $this->load->view('layout/footer'); ?>
</div>

<?php $this->load->view('components/order_upload_script'); ?>