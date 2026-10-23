
<?php
session_start();
require_once 'koneksi.php';

if (
    !isset($_SESSION['username']) ||
    ($_SESSION['role'] ?? '') !== 'siswa'
) {
    http_response_code(401);
    exit;
}

$username = $_SESSION['username'];

$stmt = mysqli_prepare(
    $koneksi,
    "UPDATE penukaran
     SET sudah_dibaca = 1
     WHERE username = ?
       AND status IN ('DISETUJUI', 'SUKSES')
       AND sudah_dibaca = 0"
);

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

http_response_code(204);
?>
