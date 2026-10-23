<?php

echo "A. PHP BERJALAN<br>";

$koneksi = mysqli_init();

echo "B. MYSQLI BERHASIL DIINIT<br>";

mysqli_options($koneksi, MYSQLI_OPT_CONNECT_TIMEOUT, 5);

echo "C. AKAN COBA CONNECT<br>";

$hasil = mysqli_real_connect(
    $koneksi,
    "localhost",
    "root",
    "",
    "",
    3306
);

echo "D. SELESAI CONNECT<br>";

if ($hasil) {
    echo "MYSQL BERHASIL TERHUBUNG!";
} else {
    echo "MYSQL GAGAL: " . mysqli_connect_error();
}

?>