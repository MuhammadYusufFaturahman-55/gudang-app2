<?php 
include '../config/database.php';
include '../includes/header.php';

$sql = "SELECT tk.*, dk.jumlah, b.nama_barang, b.sku 
        FROM Transaksi_Keluar tk
        JOIN Detail_Keluar dk ON tk.id_keluar = dk.id_keluar
        JOIN Barang b ON dk.id_barang = b.id_barang
        ORDER BY tk.id_keluar DESC";

$query = mysqli_query($conn, $sql);
?>

<div class="p-4 mb-4 bg-dark rounded-3 shadow-sm border">
    <div class="container-fluid py-2">
        <h1 class="display-6 fw-bold text-white">Riwayat Transaksi Keluar & Retur </h1>
        <a href="tambah.php" class="btn btn-danger">+ Input Transaksi Baru</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal</th>
                    <th>No DO</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Tujuan</th>
                    <th>Status / Jenis</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($query) == 0): ?>
                    <tr><td colspan="7" class="text-center text-muted p-4">Belum ada transaksi keluar.</td></tr>
                <?php endif; ?>
                
                <?php while($row = mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td><?= date('d-m-Y H:i', strtotime($row['tanggal_keluar'])); ?></td>
                    <td><span class="text-danger fw-bold"><?= $row['nomor_do']; ?></span></td>
                    <td><strong><?= $row['nama_barang']; ?></strong> <small class="text-muted">(<?= $row['sku']; ?>)</small></td>
                    
                    <td>
                        <?php if($row['keterangan'] == 'Retur Konsumen'): ?>
                            <span class="badge bg-success">+ <?= $row['jumlah']; ?></span>
                        <?php else: ?>
                            <span class="badge bg-danger">- <?= $row['jumlah']; ?></span>
                        <?php endif; ?>
                    </td>
                    
                    <td><?= $row['tujuan']; ?></td>
                    
                    <td>
                        <?php if($row['keterangan'] == 'Retur Konsumen'): ?>
                            <span class="badge bg-warning text-dark">Retur Konsumen (Stok +)</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Normal (Stok -)</span>
                        <?php endif; ?>
                    </td>
                    
                    <td><?= $row['petugas']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>