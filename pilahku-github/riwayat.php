<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'siswa') {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT jenis, berat, poin, tanggal
     FROM setoran
     WHERE username = ?
     ORDER BY id ASC"
);

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

$riwayat = [];
$total_berat = 0;
$total_poin_riwayat = 0;

while ($data = mysqli_fetch_assoc($hasil)) {
    $riwayat[] = $data;
    $total_berat += (float) $data['berat'];
    $total_poin_riwayat += (float) $data['poin'];
}

mysqli_stmt_close($stmt);

$stmt_tukar = mysqli_prepare(
    $koneksi,
    "SELECT id, jumlah, metode, status, tanggal
     FROM penukaran
     WHERE username = ?
     ORDER BY id DESC"
);

mysqli_stmt_bind_param($stmt_tukar, "s", $username);
mysqli_stmt_execute($stmt_tukar);
$hasil_tukar = mysqli_stmt_get_result($stmt_tukar);

$riwayat_tukar = [];

while ($data_tukar = mysqli_fetch_assoc($hasil_tukar)) {
    $riwayat_tukar[] = $data_tukar;
}

mysqli_stmt_close($stmt_tukar);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Setoran - PilahKu+</title>
    <style>
        

body {
    margin: 0;
    font-family: Arial, sans-serif;
    color: #333;
    background-color: #f0f8f0;
    background-image: url("images/background%20daun.jpg");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    background-repeat: no-repeat;
    min-height: 100vh;
}


        .header {
            background: #2e7d32;
            color: white;
            padding: 25px;
            text-align: center;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
        }
        .kartu {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }
        h2 { color: #2e7d32; }
        .item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .item:last-child { border-bottom: none; }
        .jenis {
            font-weight: bold;
            color: #2e7d32;
        }
        .kosong {
            text-align: center;
            color: #777;
            padding: 30px;
        }
        .tombol {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background: #2e7d32;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>♻️ PilahKu+</h1>
        <p>Riwayat Setoran Sampah</p>
    </div>

    <div class="container">
        <div class="kartu">
            <h2>📋 Riwayat Setoran</h2>

            <div style="background:#e8f5e9;padding:15px;border-radius:10px;margin-bottom:20px;">
                <p><strong>📦 Total Setoran:</strong>
                    <?php echo count($riwayat); ?> kali
                </p>
                <p><strong>⚖️ Total Berat:</strong>
                    <?php echo number_format($total_berat, 2, ',', '.'); ?> kg
                </p>
                <p><strong>⭐ Total Poin:</strong>
                    <?php echo number_format($total_poin_riwayat, 0, ',', '.'); ?> poin
                </p>
            </div>

            <?php if (empty($riwayat)): ?>
                <div class="kosong">Belum ada riwayat setoran.</div>
            <?php else: ?>
                <?php foreach ($riwayat as $i => $data): ?>
                    <div class="item">
                        <div class="jenis">
                            Setoran #<?php echo $i + 1; ?>
                        </div>
                        <p>Jenis sampah:
                            <strong><?php echo htmlspecialchars($data['jenis']); ?></strong>
                        </p>
                        <p>Berat:
                            <strong><?php echo number_format((float)$data['berat'], 2, ',', '.'); ?> kg</strong>
                        </p>
                        <p>Poin:
                            <strong><?php echo number_format((float)$data['poin'], 0, ',', '.'); ?> poin</strong>
                        </p>
                        <p>📅 Tanggal:
                            <strong><?php echo htmlspecialchars($data['tanggal']); ?></strong>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            
        </div>

        <div class="kartu">
            <h2>💸 Riwayat Penukaran Reward</h2>

            <?php if (empty($riwayat_tukar)): ?>
                <div class="kosong">Belum ada riwayat penukaran.</div>
            <?php else: ?>
                <?php foreach ($riwayat_tukar as $data_tukar): ?>
                    <div class="item">
                        <div class="jenis">
                            Penukaran #<?php echo (int)$data_tukar['id']; ?>
                        </div>

                        <p>Jumlah:
                            <strong>Rp<?php echo number_format((float)$data_tukar['jumlah'], 0, ',', '.'); ?></strong>
                        </p>

                        <p>Metode:
                            <strong><?php echo htmlspecialchars($data_tukar['metode']); ?></strong>
                        </p>

                        <p>Status:
                            <strong><?php echo htmlspecialchars($data_tukar['status']); ?></strong>
                        </p>

                        <p>Tanggal:
                            <strong><?php echo htmlspecialchars($data_tukar['tanggal']); ?></strong>
                        </p>

                        <?php if ($data_tukar['status'] === 'SUKSES'): ?>
                            <a
                                href="bukti_pdf.php?id=<?php echo (int)$data_tukar['id']; ?>"
                                class="tombol"
                                target="_blank"
                            >📄 Lihat Bukti PDF</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

            <a href="dashboard.php" class="tombol">← Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>