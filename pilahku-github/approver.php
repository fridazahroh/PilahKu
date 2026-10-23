<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "approver") {
    header("Location: login.php");
    exit;
}

$query = mysqli_query($koneksi,
    "SELECT * FROM penukaran ORDER BY id DESC"
);

if (!$query) {
    die("Gagal mengambil pengajuan: " . mysqli_error($koneksi));
}

$pengajuan = [];
while ($data = mysqli_fetch_assoc($query)) {
    $pengajuan[] = $data;
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Approver Adiwiyata - PilahKu+</title>

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


        header {
            background: #2e7d32;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .container {
            width: 90%;
            max-width: 900px;
            margin: 30px auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        h2 {
            color: #2e7d32;
            margin-top: 0;
        }

        .status {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: bold;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-disetujui {
            background: #e3f2fd;
            color: #1565c0;
        }

        .button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 16px;
            background: #2e7d32;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

        .button:hover {
            background: #1b5e20;
        }

        .info {
            color: #666;
        }
    </style>
</head>

<body>

<header>
    <h1>PilahKu+</h1>
    <p>Panel Approver Adiwiyata</p>
</header>

<div class="container">

    <div class="card">
        <h2>📋 Pengajuan Reward Siswa</h2>
        <p class="info">
            Periksa pengajuan reward siswa sebelum diteruskan kepada Admin Super.
        </p>
    </div>

    <?php if (!empty($pengajuan)): ?>

        <?php foreach ($pengajuan as $index => $data): ?>

            <div class="card">

                <h2>Pengajuan #<?php echo (int)$data['id']; ?></h2>

                <p>
                    <strong>Nama:</strong>
                    <?php echo htmlspecialchars($data['username']); ?>
                </p>

                <p>
                    <strong>Jumlah:</strong>
                    Rp<?php echo number_format($data['jumlah'], 0, ',', '.'); ?>
                </p>

                <p>
                    <strong>Metode:</strong>
                    <?php echo htmlspecialchars($data['metode']); ?>
                </p>

                <?php if ($data['metode'] === 'Transfer Bank'): ?>

                    <p>
                        <strong>Bank:</strong>
                        <?php echo htmlspecialchars($data['bank']); ?>
                    </p>

                <?php endif; ?>

                <p>
                    <strong>Nomor:</strong>
                    <?php echo htmlspecialchars($data['nomor']); ?>
                </p>

                <p>
                    <strong>Status:</strong>
                </p>

                <?php if ($data['status'] === 'PENDING'): ?>

                    <span class="status status-pending">
                        ⏳ PENDING
                    </span>

                    <br>

                    <a href="setujui.php?id=<?php echo (int)$data['id']; ?>" class="button">
                        ☑️ Setujui Pengajuan
                    </a>

                <?php elseif ($data['status'] === 'DISETUJUI'): ?>

                    <span class="status status-disetujui">
                        ☑️ DISETUJUI
                    </span>

                <?php else: ?>

                    <span class="status status-disetujui">
                        ☑️ <?php echo htmlspecialchars($data['status']); ?>
                    </span>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="card">
            <p>Belum ada pengajuan reward dari siswa.</p>
        </div>

    <?php endif; ?>

</div>

</body>
</html>