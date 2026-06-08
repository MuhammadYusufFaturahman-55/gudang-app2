<?php 
include 'config/database.php';
include 'includes/header.php';

// Query mengambil total barang masuk per bulan di tahun 2026
$query_chart_masuk = mysqli_query($conn, "
    SELECT MONTH(tm.tanggal_masuk) as bulan, SUM(dm.jumlah) as total 
    FROM Transaksi_Masuk tm
    JOIN Detail_Masuk dm ON tm.id_masuk = dm.id_masuk
    WHERE YEAR(tm.tanggal_masuk) = 2026 AND tm.keterangan != 'Retur Supplier'
    GROUP BY MONTH(tm.tanggal_masuk)
");

// Query mengambil total barang keluar per bulan di tahun 2026
$query_chart_keluar = mysqli_query($conn, "
    SELECT MONTH(tk.tanggal_keluar) as bulan, SUM(dk.jumlah) as total 
    FROM Transaksi_Keluar tk
    JOIN Detail_Keluar dk ON tk.id_keluar = dk.id_keluar
    WHERE YEAR(tk.tanggal_keluar) = 2026 AND tk.keterangan = 'Normal'
    GROUP BY MONTH(tk.tanggal_keluar)
");

// Tampung data ke dalam array PHP agar bisa dibaca oleh JavaScript (Chart.js)
$data_masuk  = array_fill(1, 12, 0); // Isi default Jan-Des dengan angka 0
$data_keluar = array_fill(1, 12, 0);

while($row = mysqli_fetch_assoc($query_chart_masuk)) {
    $data_masuk[$row['bulan']] = (int)$row['total'];
}
while($row = mysqli_fetch_assoc($query_chart_keluar)) {
    $data_keluar[$row['bulan']] = (int)$row['total'];
}

// ================= [TAMBAHAN: QUERY UNTUK GRAFIK DONAT] =================
$query_chart_donat = mysqli_query($conn, "
    SELECT l.nama_zona, SUM(sl.jumlah_stok) as total_stok 
    FROM Stok_Lokasi sl
    JOIN Lokasi l ON sl.id_lokasi = l.id_lokasi
    WHERE sl.jumlah_stok > 0
    GROUP BY l.id_lokasi
");

$label_rak = [];
$stok_rak  = [];

while($row_donat = mysqli_fetch_assoc($query_chart_donat)) {
    $label_rak[] = $row_donat['nama_zona'];
    $stok_rak[]  = (int)$row_donat['total_stok'];
}
// =========================================================================

// 1. Ambil Ringkasan Data untuk Card Informasi
$q_barang    = mysqli_query($conn, "SELECT COUNT(*) as total FROM Barang");
$data_barang = mysqli_fetch_assoc($q_barang);

$q_lokasi    = mysqli_query($conn, "SELECT COUNT(*) as total FROM Lokasi");
$data_lokasi = mysqli_fetch_assoc($q_lokasi);

// 2. Ambil Data Real-time Isi Stok di Setiap Lokasi/Rak
$sql_stok = "SELECT sl.jumlah_stok, b.nama_barang, b.sku, b.satuan, l.nama_zona 
             FROM Stok_Lokasi sl
             JOIN Barang b ON sl.id_barang = b.id_barang
             JOIN Lokasi l ON sl.id_lokasi = l.id_lokasi
             WHERE sl.jumlah_stok > 0
             ORDER BY l.nama_zona ASC";
$query_stok = mysqli_query($conn, $sql_stok);
?>

<div class="p-4 mb-4 bg-dark rounded-3 shadow-sm border">
    <div class="container-fluid py-2">
        <h1 class="display-6 fw-bold text-white">Logistik Gudang Dashboard</h1>
        <p class="col-md-12 fs-6 text-white">Sistem pemantauan stok, pelacakan posisi rak, transaksi inbound, dan outbound secara terintegrasi.</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card text-white bg-primary mb-3 shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-title text-uppercase text-white-50">Total Jenis Produk</h6>
                <p class="card-text fs-3 fw-bold mb-0"><?= $data_barang['total']; ?> Item</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-white bg-success mb-3 shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-title text-uppercase text-white-50">Total Area / Rak</h6>
                <p class="card-text fs-3 fw-bold mb-0"><?= $data_lokasi['total']; ?> Zona</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fs-6 fw-bold text-dark">📈 Grafik Tren Mutasi Barang (Tahun 2026)</h5>
            </div>
            <div class="card-body">
                <canvas id="mutasiChart" style="max-height: 320px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fs-6 fw-bold text-dark">🍩 Distribusi & Kepadatan Stok di Rak</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <?php if(empty($label_rak)): ?>
                    <p class="text-muted text-center my-5">Belum ada data stok di rak.</p>
                <?php else: ?>
                    <canvas id="donatChart" style="max-height: 280px; max-width: 280px;"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white py-3">
        <h5 class="card-title mb-0 fs-6 fw-bold">📋 Laporan Real-Time Posisi Stok di Rak</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Lokasi / Rak</th>
                    <th>Kode SKU</th>
                    <th>Nama Barang</th>
                    <th class="text-end">Jumlah Stok Tersedia</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if(mysqli_num_rows($query_stok) == 0): 
                ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted p-4">
                            Tidak ada stok barang di rak manapun saat ini. Silakan isi lewat menu Barang Masuk.
                        </td>
                    </tr>
                <?php 
                endif; 
                while($row = mysqli_fetch_assoc($query_stok)): 
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><span class="badge bg-info text-dark fw-bold"><?= $row['nama_zona']; ?></span></td>
                    <td><code><?= $row['sku']; ?></code></td>
                    <td><strong><?= $row['nama_barang']; ?></strong></td>
                    <td class="text-end fw-bold text-success"><?= number_format($row['jumlah_stok']); ?> <?= $row['satuan']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// 1. Inisialisasi Grafik Garis Mutasi
const ctxMutasi = document.getElementById('mutasiChart').getContext('2d');
const mutasiChart = new Chart(ctxMutasi, {
    type: 'line', 
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [
            {
                label: 'Barang Masuk (Inbound)',
                data: <?php echo json_encode(array_values($data_masuk)); ?>,
                borderColor: '#0d6efd', 
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.3,
                fill: true
            },
            {
                label: 'Barang Keluar (Outbound)',
                data: <?php echo json_encode(array_values($data_keluar)); ?>,
                borderColor: '#dc3545', 
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.3,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Jumlah Item' }
            }
        }
    }
});

// 2. Inisialisasi Grafik Donat Rak (Hanya berjalan jika ada data)
const ctxDonat = document.getElementById('donatChart');
if(ctxDonat) {
    const donatChart = new Chart(ctxDonat.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($label_rak); ?>,
            datasets: [{
                label: 'Total Stok',
                data: <?php echo json_encode($stok_rak); ?>,
                backgroundColor: [
                    '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6610f2', '#fd7e14', '#20c997'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 15 }
                }
            }
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>