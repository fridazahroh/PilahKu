<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'siswa') {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$jumlah = floatval($_POST['jumlah'] ?? 0);
$metode = $_POST['metode'] ?? '';
$nomor = trim($_POST['nomor'] ?? '');
$bank = $_POST['bank'] ?? '';

$stmt = mysqli_prepare($koneksi,
    "SELECT COALESCE(SUM(poin), 0) AS total_poin
     FROM setoran WHERE username = ?"
);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$reward = (float)$data['total_poin'] * 100;
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($koneksi,
    "SELECT COALESCE(SUM(jumlah), 0) AS total_tukar
     FROM penukaran
     WHERE username = ? AND status IN ('PENDING', 'SUKSES')"
);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$reward -= (float)$data['total_tukar'];
mysqli_stmt_close($stmt);

$error = false;

if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || $jumlah < 1000
    || $nomor === ''
    || !in_array($metode, ['DANA', 'GoPay', 'OVO', 'ShopeePay', 'Transfer Bank'], true)
    || ($metode === 'Transfer Bank' && !in_array($bank, ['BCA', 'BRI', 'BNI', 'Mandiri', 'BSI'], true))) {
    $error = true;
    $pesan_error = 'Data pengajuan tidak valid.';
} elseif ($jumlah > $reward) {
    $error = true;
    $pesan_error = 'Saldo tidak cukup.';
} else {
    $stmt = mysqli_prepare($koneksi,
        "INSERT INTO penukaran (username, jumlah, metode, bank, nomor, status)
         VALUES (?, ?, ?, NULLIF(?, ''), ?, 'PENDING')"
    );
    mysqli_stmt_bind_param($stmt, "sdsss", $username, $jumlah, $metode, $bank, $nomor);

    if (!mysqli_stmt_execute($stmt)) {
        $error = true;
        $pesan_error = 'Pengajuan gagal disimpan ke database.';
    } else {
        $reward -= $jumlah;
    }
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
            margin: 60px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-sizing: border-box;
        }

        h1 {
            color: #2e7d32;
        }

        .status {
            background-color: #fff8e1;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .status strong {
            color: #f57c00;
            font-size: 24px;
        }

        .info {
            text-align: left;
            background-color: #f0f8f0;
            padding: 15px;
            border-radius: 10px;
        }

        .tombol {
            display: block;
            margin-top: 20px;
            padding: 12px;
            background-color: #2e7d32;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <div class="kotak">

        <h1>💰 Penukaran Reward</h1>

        <?php if ($error): ?>

<div class="status">
    <p>Penukaran gagal</p>
<strong>❌ PENUKARAN GAGAL</strong>
<p><?php echo htmlspecialchars($pesan_error ?? 'Pengajuan gagal.'); ?></p>
<p>Saldo kamu tersisa Rp<?php echo number_format($reward, 0, ',', '.'); ?>.</p>
</div>

<?php else: ?>

<div class="status">
    <p>Status pengajuan:</p>
    <strong>⏳ PENDING</strong>
    <p>Menunggu proses admin.</p>
</div>

<?php endif; ?>

        <div class="info">

            <p>
                <strong>Jumlah:</strong>
                Rp<?php echo htmlspecialchars($jumlah); ?>
            </p>

            <p>
                <strong>Metode:</strong>
                <?php echo htmlspecialchars($metode); ?>
            </p>

            <?php if ($metode === 'Transfer Bank'): ?>

            <p>
                <strong>Bank:</strong>
                <?php echo htmlspecialchars($bank); ?>
            </p>

            <?php endif; ?>

            <p>
                <strong>Nomor:</strong>
                <?php echo htmlspecialchars($nomor); ?>
            </p>

        </div>

        <a href="dashboard.php" class="tombol">
            Kembali ke Dashboard
        </a>

    </div>

</body>
</html>