<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$berhasil = false;

if ($id && $id > 0) {
    $stmt = mysqli_prepare($koneksi,
        "UPDATE penukaran SET status = 'SUKSES'
         WHERE id = ? AND status = 'DISETUJUI'"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $berhasil = mysqli_stmt_affected_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Penukaran - PilahKu+</title>
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

        .kotak {
            width: 90%;
            max-width: 500px;
            margin: 70px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-sizing: border-box;
        }
        h1 { color: #2e7d32; }
        .status {
            background: #e8f5e9;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .tombol {
            display: block;
            margin-top: 20px;
            padding: 12px;
            background: #2e7d32;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="kotak">
    <h1>💰 Penukaran Reward</h1>

    <?php if ($berhasil): ?>
        <div class="status">
            <strong>✅ SUKSES</strong>
            <p>Status pengajuan berhasil diperbarui.</p>
        </div>
        <p>Terima kasih sudah berpartisipasi memilah sampah bersama PilahKu+ 🌱</p>
    <?php else: ?>
        <div class="status">
            <strong>⚠️ Belum dapat diproses</strong>
            <p>Pengajuan tidak ditemukan atau belum disetujui Approver.</p>
        </div>
    <?php endif; ?>

    <a href="admin.php" class="tombol">← Kembali ke Admin</a>
</div>
</body>
</html>