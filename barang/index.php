<?php 
include '../config/database.php';
include '../includes/header.php';

$query = mysqli_query($conn, "SELECT * FROM Barang ORDER BY id_barang DESC");
?>

<div class="p-4 mb-4 bg-dark rounded-3 shadow-sm border">
    <div class="container-fluid py-2">
        <h1 class="display-6 fw-bold text-primary">Logistik Gudang Dashboard</h1>
        <a href="tambah.php" class="btn btn-success">+ Tambah Barang Baru</a>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Daftar Barang di Gudang</h2>
    <a href="tambah.php" class="btn btn-success">+ Tambah Barang Baru</a>
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
                    <th>Stok Minimum</th>
                    <th>Satuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($query)): 
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><span class="badge bg-secondary"><?= $row['sku']; ?></span></td>
                    <td><strong><?= $row['nama_barang']; ?></strong></td>
                    <td><?= $row['kategori']; ?></td>
                    <td><?= $row['stok_minimum']; ?></td>
                    <td><?= $row['satuan']; ?></td>
                    <td>
                        <a href="hapus.php?id=<?= $row['id_barang']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>