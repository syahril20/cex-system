<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Data Order</h1>

            <?php
            // Ambil id dari path (misal: /order_detail/{id})
            $id_order = $this->uri->segment(3); // sesuaikan segment jika perlu

            // Query ke database untuk ambil data order
            $query = $this->db->get_where('orders', ['id' => $id_order]);
            $order = $query->row_array();
            ?>

            <?php if (isset($order) && !empty($order)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Detail Order</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID Order</th>
                                <td><?= htmlspecialchars($order['id']) ?></td>
                            </tr>
                            <tr>
                                <th>User ID</th>
                                <td><?= htmlspecialchars($order['user_id']) ?></td>
                            </tr>
                            <tr>
                                <th>Data</th>
                                <td>
                                    <?php
                                    $data = json_decode($order['data'], true);
                                    if (is_array($data)) {
                                        echo '<table class="table table-sm table-bordered">';
                                        foreach ($data as $key => $value) {
                                            if ($key === 'shipment_details' && is_array($value)) {
                                                echo '<tr><th>' . htmlspecialchars($key) . '</th><td>';
                                                echo '<table class="table table-sm table-bordered">';
                                                foreach ($value as $idx => $detail) {
                                                    if ($idx > 0) {
                                                        // Tambahkan jarak antar item
                                                        echo '<tr><td colspan="2" style="height:20px;"></td></tr>';
                                                    }
                                                    echo '<tr><td colspan="2"><strong>Item ' . ($idx + 1) . '</strong></td></tr>';
                                                    foreach ($detail as $dKey => $dVal) {
                                                        echo '<tr><th style="width:120px;">' . htmlspecialchars($dKey) . '</th><td>' . htmlspecialchars($dVal) . '</td></tr>';
                                                    }
                                                }
                                                echo '</table></td></tr>';
                                            } else {
                                                echo '<tr><th>' . htmlspecialchars($key) . '</th><td>' . htmlspecialchars($value) . '</td></tr>';
                                            }
                                        }
                                        echo '</table>';
                                    } else {
                                        echo htmlspecialchars($order['data']);
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td><?= htmlspecialchars($order['created_at']) ?></td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td><?= htmlspecialchars($order['updated_at']) ?></td>
                            </tr>
                            <tr>
                                <th>Created By</th>
                                <td><?= htmlspecialchars($order['created_by']) ?></td>
                            </tr>
                            <tr>
                                <th>Updated By</th>
                                <td><?= htmlspecialchars($order['updated_by']) ?></td>
                            </tr>
                            <tr>
                                <th>Airwaybill</th>
                                <td><?= htmlspecialchars($order['airwaybill']) ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><?= htmlspecialchars($order['status']) ?></td>
                            </tr>
                            <tr>
                                <th>Shipment Images</th>
                                <td>
                                    <?php
                                    $images = $this->db->get_where('shipment_images', ['order_id' => $order['id']])->result_array();
                                    $hasImage = false;
                                    if (!empty($images)) {
                                        foreach ($images as $img) {
                                            $img_url = base_url(ltrim($img['file_path'], '/'));
                                            // Cek apakah file benar-benar ada di server
                                            $file_path = FCPATH . ltrim($img['file_path'], '/');
                                            if (is_file($file_path)) {
                                                echo '<img src="' . htmlspecialchars($img_url) . '" alt="Shipment Image" style="max-width:120px;max-height:120px;margin:5px;">';
                                                $hasImage = true;
                                            }
                                        }
                                    }
                                    if (!$hasImage) {
                                        echo 'No images available or failed to load.<br>';
                                        // Button upload ulang
                                        echo '<a href="' . base_url('order/upload_shipment_form/' . $order['id']) . '" class="btn btn-sm btn-primary">Upload Ulang</a>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">Data order tidak ditemukan.</div>
            <?php endif; ?>
        </div>
    </main>
</div>