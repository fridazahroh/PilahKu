
<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'siswa') {
    die('Silakan login sebagai siswa terlebih dahulu.');
}

$jenis = $_POST['jenis'] ?? '';
$berat = floatval($_POST['berat'] ?? 0);
$username = $_SESSION['username'];

$jenis_valid = ['plastik', 'kertas', 'kaleng', 'lainnya'];

if (!in_array($jenis, $jenis_valid, true) || $berat <= 0) {
    die('Data setoran tidak valid.');
}

$poin = $berat * 10;

$stmt = mysqli_prepare(
    $koneksi,
    "INSERT INTO setoran (username, jenis, berat, poin)
     VALUES (?, ?, ?, ?)"
);

mysqli_stmt_bind_param($stmt, "ssdd", $username, $jenis, $berat, $poin);

if (!mysqli_stmt_execute($stmt)) {
    die('Setoran gagal disimpan: ' . mysqli_stmt_error($stmt));
}

if (!isset($_SESSION['riwayat'])) {
    $_SESSION['riwayat'] = [];
}

$_SESSION['riwayat'][] = [
    'jenis' => $jenis,
    'berat' => $berat,
    'poin' => $poin,
    'tanggal' => date('d-m-Y')
];

$_SESSION['total_sampah'] = ($_SESSION['total_sampah'] ?? 0) + $berat;
$_SESSION['total_poin'] = ($_SESSION['total_poin'] ?? 0) + $poin;
$_SESSION['reward'] = ($_SESSION['reward'] ?? 0) + ($poin * 100);

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setoran Berhasil - PilahKu+</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f8f0;
        }
        .kotak {
            width: 90%;
            max-width: 500px;
            margin: 70px auto;
            background: white;
            padding: 30px;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        h1 { color: #2e7d32; }
        .hasil {
            background-color: #e8f5e9;
            padding: 18px;
            border-radius: 12px;
            margin: 20px 0;
        }
        .poin {
            font-size: 30px;
            font-weight: bold;
            color: #2e7d32;
        }
        .tombol {
            display: block;
            margin-top: 20px;
            padding: 13px;
            background-color: #2e7d32;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="kotak">
        <h1>♻️ Setoran Berhasil!</h1>
        <div class="hasil">
            <p>Jenis sampah:</p>
            <strong><?php echo htmlspecialchars($jenis); ?></strong>
            <p>Berat sampah:</p>
            <strong><?php echo htmlspecialchars((string)$berat); ?> kg</strong>
        </div>
        <p>Poin yang kamu dapat:</p>
        <div class="poin">
            +<?php echo htmlspecialchars((string)$poin); ?> Poin
        </div>
        <a href="dashboard.php" class="tombol">Kembali ke Dashboard</a>
    </div>
</body>
</html>
