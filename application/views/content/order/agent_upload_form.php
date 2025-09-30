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
                    <form method="post" action="<?= site_url('order/do_upload'); ?>" enctype="multipart/form-data">

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
                            <input type="file" name="filename" class="form-control" accept="image/*" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>

        </div>
    </main>
    <?php $this->load->view('layout/footer'); ?>
</div>