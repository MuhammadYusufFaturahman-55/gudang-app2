<?php 
include '../config/database.php';
include '../includes/header.php';

if (isset($_POST['submit'])) {
    $nama_barang  = $_POST['nama_barang'];
    $sku          = $_POST['sku'];
    $kategori     = $_POST['kategori'];
    $stok_minimum = $_POST['stok_minimum'];
    $satuan       = $_POST['satuan'];

    $insert = mysqli_query($conn, "INSERT INTO Barang (nama_barang, sku, kategori, stok_minimum, satuan) 
                                   VALUES ('$nama_barang', '$sku', '$kategori', '$stok_minimum', '$satuan')");

    if ($insert) {
        echo "<script>alert('Barang berhasil ditambahkan!'); window.location='index.php';</script>";
    } else {
      echo "<div class='alert alert-danger'>Gagal menambahkan barang: " . mysqli_error($conn) . "</div>";    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white"><h5>Form Tambah Barang</h5></div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Kode SKU (Harus Unik)</label>
                        <input type="text" name="sku" class="form-control" placeholder="Contoh: SKU-001" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="kategori" class="form-control" placeholder="Elektronik, Pakaian, dll.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok Minimum</label>
                        <input type="number" name="stok_minimum" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="satuan" class="form-control" placeholder="Pcs, Box, Kg" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary w-100">Simpan Barang</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>