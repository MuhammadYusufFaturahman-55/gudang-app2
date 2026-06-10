<?php
include '../config/database.php';

$id_barang = $_GET['id'];

// 1. Cek apakah barang ini sudah pernah tercatat di Transaksi Masuk
$cek_masuk = mysqli_query($conn, "SELECT COUNT(*) as total FROM Detail_Masuk WHERE id_barang = '$id_barang'");
$data_masuk = mysqli_fetch_assoc($cek_masuk);

// 2. Cek apakah barang ini sudah pernah tercatat di Transaksi Keluar
$cek_keluar = mysqli_query($conn, "SELECT COUNT(*) as total FROM Detail_Keluar WHERE id_barang = '$id_barang'");
$data_keluar = mysqli_fetch_assoc($cek_keluar);

// 3. JIKA SUDAH ADA TRANSAKSI (Masuk atau Keluar), TOLAK PENGHAPUSAN
if ($data_masuk['total'] > 0 || $data_keluar['total'] > 0) {
    echo "<script>
            alert('Gagal Menghapus! Barang ini tidak boleh dihapus karena sudah memiliki riwayat aktivitas transaksi masuk/keluar demi keamanan data audit gudang.');
            window.location='index.php';
          </script>";
    exit;
}

// 4. JIKA BERSIH (Belum ada transaksi sama sekali), BARU BOLEH DIHAPUS
// Hapus dulu data di tabel jembatan stok lokasi (jika ada)
mysqli_query($conn, "DELETE FROM Stok_Lokasi WHERE id_barang = '$id_barang'");

// Hapus data utama barang
$delete = mysqli_query($conn, "DELETE FROM Barang WHERE id_barang = '$id_barang'");

if ($delete) {
    echo "<script>
            alert('Barang berhasil dihapus dari sistem.');
            window.location='index.php';
          </script>";
} else {
    echo "Gagal menghapus barang: " . mysqli_error($conn);
}
?>