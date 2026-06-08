<?php 
include '../config/database.php';
include '../includes/header.php';

if (isset($_POST['submit'])) {
    $nama_zona = $_POST['nama_zona'];
    $kapasitas = $_POST['kapasitas']; // Mengambil nilai angka dari input form

    // PERBAIKAN: Target penyimpanan diubah ke kolom 'kapasitas_maksimal'
    $insert = mysqli_query($conn, "INSERT INTO Lokasi (nama_zona, kapasitas_maksimal) VALUES ('$nama_zona', '$kapasitas')");

    if ($insert) {
        echo "<script>alert('Lokasi berhasil ditambahkan!'); window.location='index.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menambahkan lokasi: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white"><h5>Form Tambah Lokasi Gudang</h5></div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Zona / Blok / Rak</label>
                        <input type="text" name="nama_zona" class="form-control" placeholder="Contoh: Blok A-01, Rak Elektronik" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas Tampung (Angka)</label>
                        <input type="number" name="kapasitas" class="form-control" placeholder="Contoh: 500" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary w-100">Simpan Lokasi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>