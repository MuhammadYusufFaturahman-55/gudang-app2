<?php 
include '../config/database.php';
include '../includes/header.php';

// AMBIL DATA KATEGORI UNTUK DROPDOWN
$kategori_query = mysqli_query($conn, "SELECT * FROM Kategori ORDER BY nama_kategori ASC");

if (isset($_POST['submit'])) {
    $sku           = $_POST['sku'];
    $nama_barang   = $_POST['nama_barang'];
    $id_kategori   = $_POST['id_kategori']; // Menangkap ID atau Nama Kategori dari dropdown
    $satuan        = $_POST['satuan'];

    // Query simpan data barang (Sesuaikan nama kolom 'id_kategori' atau 'kategori' dengan database Anda)
    $insert = mysqli_query($conn, "INSERT INTO Barang (sku, nama_barang, kategori, satuan) 
                                   VALUES ('$sku', '$nama_barang', '$id_kategori', '$satuan')");

    if ($insert) {
        echo "<script>alert('Barang baru berhasil ditambahkan!'); window.location='index.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menambahkan barang: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title mb-0">📦 Form Tambah Produk / Barang Baru</h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode SKU Barang</label>
                        <input type="text" name="sku" class="form-control" placeholder="Contoh: SKU-1002" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Lampu LED 12W" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori Produk</label>
                        <select name="id_kategori" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php while($kat = mysqli_fetch_assoc($kategori_query)): ?>
                                <option value="<?= $kat['nama_kategori']; ?>"><?= $kat['nama_kategori']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Satuan Barang</label>
                        <select name="satuan" class="form-select" required>
                            <option value="">-- Pilih Satuan --</option>
                            <option value="Pcs">Pcs (Pieces)</option>
                            <option value="Dus">Dus / Karton</option>
                            <option value="Unit">Unit</option>
                            <option value="Pack">Pack</option>
                        </select>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary w-100 py-2 fw-bold">Simpan Produk Baru</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>