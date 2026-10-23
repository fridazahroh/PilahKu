<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "approver") {
    header("Location: login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id && $id > 0) {
    $stmt = mysqli_prepare($koneksi,
        "UPDATE penukaran
         SET status = 'DISETUJUI'
         WHERE id = ? AND status = 'PENDING'"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: approver.php");
exit;
?>