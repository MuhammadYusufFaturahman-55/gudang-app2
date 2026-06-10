<?php 
include '../config/database.php';
include '../includes/header.php';

// 1. Ambil ID Barang dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id_barang = $_GET['id'];

// 2. Tarik data barang sekaligus cek lokasi raknya saat ini dari tabel Stok_Lokasi
$sql_barang = "SELECT b.*, sl.id_lokasi 
               FROM Barang b 
               LEFT JOIN Stok_Lokasi sl ON b.id_barang = sl.id_barang 
               WHERE b.id_barang = '$id_barang'";
$result = mysqli_query($conn, $sql_barang);
$barang = mysqli_fetch_assoc($result);

if (!$barang) {
    echo "<div class='alert alert-danger'>Data barang tidak ditemukan!</div>";
    exit;
}

// 3. Ambil semua daftar kategori untuk dropdown
$kategori_query = mysqli_query($conn, "SELECT * FROM Kategori ORDER BY nama_kategori ASC");

// 4. Ambil semua daftar lokasi rak untuk dropdown pindah rak
$lokasi_query = mysqli_query($conn, "SELECT * FROM Lokasi ORDER BY nama_zona ASC");

// 5. Proses Update saat tombol Simpan Perubahan diklik
if (isset($_POST['update'])) {
    $sku         = $_POST['sku'];
    $nama_barang = $_POST['nama_barang'];
    $kategori    = $_POST['kategori'];
    $satuan      = $_POST['satuan'];
    $rak_baru    = $_POST['id_lokasi']; // Menangkap ID Rak baru pilihan user
    $rak_lama    = $barang['id_lokasi']; // ID Rak lama sebelum diedit

    // Mulai Database Transaksi agar proses aman
    mysqli_begin_transaction($conn);

    try {
        // A. Update data utama barang
        mysqli_query($conn, "UPDATE Barang SET sku = '$sku', nama_barang = '$nama_barang', kategori = '$kategori', satuan = '$satuan' WHERE id_barang = '$id_barang'");

        // B. Logika Pemindahan Rak di tabel Stok_Lokasi
        if (!empty($rak_baru)) {
            if (empty($rak_lama)) {
                // Jika sebelumnya barang ini belum punya rak (stok awal 0), buat baris baru
                mysqli_query($conn, "INSERT INTO Stok_Lokasi (id_barang, id_lokasi, jumlah_stok) VALUES ('$id_barang', '$rak_baru', 0)");
            } elseif ($rak_lama != $rak_baru) {
                // Jika user memilih rak yang berbeda dari sebelumnya, ubah lokasinya
                mysqli_query($conn, "UPDATE Stok_Lokasi SET id_lokasi = '$rak_baru' WHERE id_barang = '$id_barang' AND id_lokasi = '$rak_lama'");
            }
        }

        // Jika semua query sukses, terapkan ke database
        mysqli_commit($conn);
        echo "<script>alert('Data barang dan posisi rak berhasil diperbarui!'); window.location='index.php';</script>";

    } catch (Exception $e) {
        // Jika ada yang gagal, batalkan semua perubahan
        mysqli_rollback($conn);
        echo "<div class='alert alert-danger'>Gagal memperbarui data: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark py-3">
                <h5 class="card-title mb-0 fw-bold">✏️ Form Edit Produk & Pindah Rak</h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode SKU Barang</label>
                        <input type="text" name="sku" class="form-control" value="<?= $barang['sku']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" value="<?= $barang['nama_barang']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori Produk</label>
                        <select name="kategori" class="form-select" required>
                            <?php while($kat = mysqli_fetch_assoc($kategori_query)): ?>
                                <option value="<?= $kat['nama_kategori']; ?>" <?= ($barang['kategori'] == $kat['nama_kategori']) ? 'selected' : ''; ?>>
                                    <?= $kat['nama_kategori']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Satuan Barang</label>
                        <select name="satuan" class="form-select" required>
                            <option value="Pcs" <?= ($barang['satuan'] == 'Pcs') ? 'selected' : ''; ?>>Pcs (Pieces)</option>
                            <option value="Dus" <?= ($barang['satuan'] == 'Dus') ? 'selected' : ''; ?>>Dus / Karton</option>
                            <option value="Unit" <?= ($barang['satuan'] == 'Unit') ? 'selected' : ''; ?>>Unit</option>
                            <option value="Pack" <?= ($barang['satuan'] == 'Pack') ? 'selected' : ''; ?>>Pack</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-primary"> Posisi Rak Gudang (Pindah Rak)</label>
                        <select name="id_lokasi" class="form-select border-primary" required>
                            <option value="">-- Pilih Lokasi Rak --</option>
                            <?php while($lok = mysqli_fetch_assoc($lokasi_query)): ?>
                                <option value="<?= $lok['id_lokasi']; ?>" <?= ($barang['id_lokasi'] == $lok['id_lokasi']) ? 'selected' : ''; ?>>
                                    <?= $lok['nama_zona']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="form-text text-muted">Mengubah pilihan ini akan memindahkan seluruh stok barang ini ke rak yang baru.</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="index.php" class="btn btn-secondary w-50 py-2 fw-bold">Batal</a>
                        <button type="submit" name="update" class="btn btn-warning w-50 py-2 fw-bold text-dark">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>