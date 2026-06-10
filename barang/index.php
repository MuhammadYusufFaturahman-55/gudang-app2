<?php 
include '../config/database.php';
include '../includes/header.php';

// QUERY UTAMA: Mengambil data barang, menjumlahkan stok, dan menggabungkan lokasi rak terkait
$sql = "SELECT b.*, 
               IFNULL(SUM(sl.jumlah_stok), 0) as total_stok,
               GROUP_CONCAT(DISTINCT l.nama_zona SEPARATOR ', ') as lokasi_rak
        FROM Barang b
        LEFT JOIN Stok_Lokasi sl ON b.id_barang = sl.id_barang
        LEFT JOIN Lokasi l ON sl.id_lokasi = l.id_lokasi
        GROUP BY b.id_barang
        ORDER BY b.id_barang DESC";

$query = mysqli_query($conn, $sql);
?>

<div class="p-4 mb-4 bg-dark rounded-3 shadow-sm border">
    <div class="container-fluid py-2">
        <h1 class="display-6 fw-bold text-white">Daftar Barang Di Gudang</h1>
        <a href="tambah.php" class="btn btn-success">+ Tambah Barang Baru</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>SKU</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th>Lokasi Rak</th>
                    <th class="text-end">Total Stok</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if(mysqli_num_rows($query) == 0):
                ?>
                    <tr><td colspan="8" class="text-center text-muted p-4">Belum ada data barang.</td></tr>
                <?php 
                endif;
                while($row = mysqli_fetch_assoc($query)): 
                    $stok = (int)$row['total_stok'];
                    $rak  = $row['lokasi_rak'];
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><span class="badge bg-secondary"><?= $row['sku']; ?></span></td>
                    <td><strong><?= $row['nama_barang']; ?></strong></td>
                    <td><?= $row['kategori']; ?></td>
                    <td><?= $row['satuan']; ?></td>
                    
                    <td>
                        <?php if(!empty($rak)): ?>
                            <span class="badge bg-info text-dark"><?= $rak; ?></span>
                        <?php else: ?>
                            <span class="text-muted small">Belum di-plotting</span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="text-end fw-bold">
                        <?php if($stok == 0): ?>
                            <span class="text-danger">0</span>
                        <?php else: ?>
                            <span class="text-success"><?= number_format($stok); ?></span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <a href="edit.php?id=<?= $row['id_barang']; ?>" class="btn btn-warning btn-sm fw-bold">Edit</a>
                            
                            <a href="hapus.php?id=<?= $row['id_barang']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>