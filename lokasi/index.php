<?php 
include '../config/database.php';
include '../includes/header.php';

// Query mengambil data lokasi dan menghitung total item yang tersimpan di rak tersebut saat ini
$sql = "SELECT l.*, IFNULL(SUM(sl.jumlah_stok), 0) as total_terisi 
        FROM Lokasi l
        LEFT JOIN Stok_Lokasi sl ON l.id_lokasi = sl.id_lokasi
        GROUP BY l.id_lokasi
        ORDER BY l.nama_zona ASC";

$query = mysqli_query($conn, $sql);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Daftar Lokasi / Rak Gudang</h2>
    <a href="tambah.php" class="btn btn-success">+ Tambah Lokasi Baru</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Zona / Rak</th>
                    <th>Kapasitas Maksimal</th>
                    <th>Total Item Terisi</th>
                    <th>Status Kepadatan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if(mysqli_num_rows($query) == 0): 
                ?>
                    <tr><td colspan="5" class="text-center text-muted p-4">Belum ada data lokasi rak.</td></tr>
                <?php 
                endif; 
                while($row = mysqli_fetch_assoc($query)): 
                    // Menggunakan kolom kapasitas_maksimal yang baru
                    $maksimal = (int)$row['kapasitas_maksimal'];
                    $terisi   = (int)$row['total_terisi'];
                    
                    // Hitung persentase keterisian rak
                    $persen = $maksimal > 0 ? round(($terisi / $maksimal) * 100) : 0;
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><strong><?= $row['nama_zona']; ?></strong></td>
                    
                    <td><?= number_format($maksimal); ?> pcs</td>
                    <td><?= number_format($terisi); ?> pcs</td>
                    
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="progress w-100 me-2" style="height: 10px;">
                                <?php 
                                $bg_color = 'bg-success';
                                if($persen >= 90) { $bg_color = 'bg-danger'; }
                                elseif($persen >= 70) { $bg_color = 'bg-warning'; }
                                ?>
                                <div class="progress-bar <?= $bg_color; ?>" role="progressbar" style="width: <?= $persen; ?>%"></div>
                            </div>
                            <span class="fw-bold small"><?= $persen; ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>