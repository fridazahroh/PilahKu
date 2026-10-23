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
    "SELECT COALESCE(SUM(poin), 0) AS total_poin
     FROM setoran
     WHERE username = ?"
);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$reward_didapat = (float)$data['total_poin'] * 100;
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT COALESCE(SUM(jumlah), 0) AS total_tukar
     FROM penukaran
     WHERE username = ?
       AND status IN ('PENDING', 'DISETUJUI', 'SUKSES')"
);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$data_tukar = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$total_tukar = (float)$data_tukar['total_tukar'];
$reward = max(0, $reward_didapat - $total_tukar);

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tukar Reward - PilahKu+</title>

    <style>
        

body {
    margin: 0;
    font-family: Arial, sans-serif;
    color: #333;
    background-color: #f0f8f0;
    background-image: url("images/background daun.jpg");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    background-repeat: no-repeat;
}



        .kotak {
            width: 90%;
            max-width: 500px;
            margin: 60px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            color: #2e7d32;
        }

        .saldo {
            background: #f0f8f0;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }

        .saldo strong {
            display: block;
            font-size: 28px;
            color: #2e7d32;
            margin-top: 8px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .kembali {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #2e7d32;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="kotak">

        <h1>💰 Tukar Reward</h1>

        <div class="saldo">
            Saldo kamu
            <strong>
                Rp<?php echo number_format($reward, 0, ',', '.'); ?>
            </strong>
        </div>

        <form action="tukar_proses.php" method="post">

            <label>Jumlah penukaran</label>

            <input
                type="number"
                name="jumlah"
                min="1000"
                placeholder="Contoh: 5000"
                required
            >

            <label>Metode penerimaan</label>

            <select name="metode" required>
            <option value="">Pilih metode</option>
            <option value="DANA">DANA</option>
            <option value="GoPay">GoPay</option>
            <option value="OVO">OVO</option>
            <option value="ShopeePay">ShopeePay</option>
            <option value="Transfer Bank">Transfer Bank</option>
        </select>

            <div id="bank-container" style="display: none;">

        <label>Nama Bank</label>

        <select name="bank">
            <option value="">Pilih bank</option>
            <option value="BCA">BCA</option>
            <option value="BRI">BRI</option>
            <option value="BNI">BNI</option>
            <option value="Mandiri">Mandiri</option>
            <option value="BSI">BSI</option>
        </select>

</div>

        <label>Nomor akun</label>

        <input
            type="text"
            name="nomor"
            placeholder="Masukkan nomor"
            required
        >

            <button type="submit">
                Ajukan Penukaran
            </button>

        </form>

        <a href="dashboard.php" class="kembali">
            ← Kembali ke Dashboard
        </a>

    </div>

    <script>
    const metode = document.querySelector('select[name="metode"]');
    const bankContainer = document.getElementById('bank-container');
    const bank = document.querySelector('select[name="bank"]');

    metode.addEventListener('change', function () {

        if (this.value === 'Transfer Bank') {
            bankContainer.style.display = 'block';
            bank.required = true;
        } else {
            bankContainer.style.display = 'none';
            bank.required = false;
            bank.value = '';
        }

    });
</script>

</body>
</html>