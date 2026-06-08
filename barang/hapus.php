<?php
include '../config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete = mysqli_query($conn, "DELETE FROM Barang WHERE id_barang = '$id'");

    if ($delete) {
        echo "<script>alert('Barang berhasil dihapus!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus barang!'); window.location='index.php';</script>";
    }
} else {
    header("Location: index.php");
}
?>