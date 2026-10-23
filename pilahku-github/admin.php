<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
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

    <title>Admin Super - PRO-PILAH MANELA</title>

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
            padding: 20px;
            text-align: center;
        }

        .container {
            width: 90%;
            max-width: 900px;
            margin: 30px auto;
        }

        .kartu {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        h2 {
            color: #2e7d32;
        }

        .status {
            display: inline-block;
            padding: 7px 12px;
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

        .status-sukses {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .tombol {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #2e7d32;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

        .tombol:hover {
            background: #1b5e20;
        }

        .info {
            color: #666;
        }
    </style>
</head>

<body>

<div class="header">
    <h1>PRO-PILAH MANELA</h1>
    <p>Dashboard Admin Super</p>
</div>

<div class="container">

    <div class="kartu">
        <h2>📋 Pengajuan Reward</h2>
        <p class="info">
            Admin Super dapat memproses pengajuan yang telah disetujui oleh Approver Adiwiyata.
        </p>
    </div>

    <?php if (!empty($pengajuan)): ?>

        <?php foreach ($pengajuan as $index => $data): ?>

            <div class="kartu">

                <h2>📋 Pengajuan Reward #<?php echo (int)$data['id']; ?></h2>

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

                <?php
                $status = $data['status'];

                if ($status === 'SUKSES') {
                    $class = 'status-sukses';
                    $label = '✅ SUKSES';
                } elseif ($status === 'DISETUJUI') {
                    $class = 'status-disetujui';
                    $label = '☑️ DISETUJUI';
                } else {
                    $class = 'status-pending';
                    $label = '⏳ PENDING';
                }
                ?>

                <span class="status <?php echo $class; ?>">
                    <?php echo $label; ?>
                </span>

                <?php if ($status === 'DISETUJUI'): ?>

                    <br>

                    
<a href="sukses.php?id=<?php echo (int)$data['id']; ?>" class="tombol">
    Tandai sebagai Sukses
</a>


                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="kartu">
            <p>Belum ada pengajuan reward.</p>
        </div>

    <?php endif; ?>

</div>

</body>
</html>