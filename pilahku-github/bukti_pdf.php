
<?php
session_start();
require_once 'koneksi.php';
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'siswa') {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit('Nomor pengajuan tidak valid.');
}

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, username, jumlah, metode, bank, nomor, status, tanggal
     FROM penukaran
     WHERE id = ? AND username = ?"
);

mysqli_stmt_bind_param($stmt, "is", $id, $username);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);

if (!$data) {
    exit('Data penukaran tidak ditemukan.');
}

$options = new Options();
$options->set('isRemoteEnabled', false);

$pdf = new Dompdf($options);

$tanggal = date('d-m-Y H:i', strtotime($data['tanggal']));
$pathLogo = __DIR__ . '/images/logo pilahku.jpg';
$logoBase64 = '';

if (file_exists($pathLogo)) {
    $logoBase64 = 'data:image/jpeg;base64,' .
        base64_encode(file_get_contents($pathLogo));
}
$jumlah = number_format((float)$data['jumlah'], 0, ',', '.');

$metode = htmlspecialchars($data['metode'], ENT_QUOTES, 'UTF-8');
$bank = htmlspecialchars($data['bank'] ?? '', ENT_QUOTES, 'UTF-8');
$nomor = htmlspecialchars($data['nomor'], ENT_QUOTES, 'UTF-8');
$status = htmlspecialchars($data['status'], ENT_QUOTES, 'UTF-8');
$nama = htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8');

$html = '
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        color: #26352a;
    }

    .kartu {
        border: 2px solid #2e7d32;
        padding: 25px;
    }

   .judul-logo {
    text-align: center;
    margin-bottom: 10px;
}

.judul-logo img {
    width: 55px;
    height: auto;
    vertical-align: middle;
}

.judul-logo span {
    color: #2e7d32;
    font-size: 28px;
    font-weight: bold;
    vertical-align: middle;
}

    .subjudul {
        text-align: center;
        color: #666;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    td:first-child {
        width: 40%;
        font-weight: bold;
    }

    .catatan {
        margin-top: 25px;
        font-size: 11px;
        color: #555;
    }
</style>
</head>
<body>
<div class="judul-logo">
    ' . ($logoBase64 !== '' ? '<img src="' . $logoBase64 . '" alt="Logo PilahKu+">' : '') . '
    <span>PilahKu+</span>
</div>
    <p class="subjudul">BUKTI PENUKARAN REWARD</p>

    <table>
        <tr>
            <td>Nomor Pengajuan</td>
            <td>#' . (int)$data['id'] . '</td>
        </tr>
        <tr>
            <td>Nama Pengguna</td>
            <td>' . $nama . '</td>
        </tr>
        <tr>
            <td>Jumlah Penukaran</td>
            <td>Rp' . $jumlah . '</td>
        </tr>
        <tr>
            <td>Metode Pembayaran</td>
            <td>' . $metode . '</td>
        </tr>
        <tr>
            <td>Bank</td>
            <td>' . $bank . '</td>
        </tr>
        <tr>
            <td>Nomor Tujuan</td>
            <td>' . $nomor . '</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>' . $status . '</td>
        </tr>
        <tr>
            <td>Tanggal Pengajuan</td>
            <td>' . $tanggal . '</td>
        </tr>
    </table>

    <p class="catatan">
        Dokumen ini merupakan bukti data pengajuan penukaran
        reward pada website PilahKu+. Pembayaran diproses secara
        manual oleh admin. Status SUKSES menunjukkan pengajuan
        telah ditandai selesai oleh admin.
    </p>
</div>
</body>
</html>';

$pdf->loadHtml($html, 'UTF-8');
$pdf->setPaper('A4', 'portrait');
$pdf->render();

$namaFile = 'bukti-penukaran-' . (int)$data['id'] . '.pdf';

$pdf->stream($namaFile, ['Attachment' => true]);
exit;
?>
