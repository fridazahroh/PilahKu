<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'siswa') {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];


 // Mengambil notifikasi pengajuan penukaran
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, jumlah, status, sudah_dibaca
     FROM penukaran
     WHERE username = ?
       AND status IN ('DISETUJUI', 'SUKSES')
     ORDER BY tanggal DESC
     LIMIT 5"
);

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$hasil_notifikasi = mysqli_stmt_get_result($stmt);

$notifikasi = [];
$jumlah_belum_dibaca = 0;

while ($baris = mysqli_fetch_assoc($hasil_notifikasi)) {
    $notifikasi[] = $baris;

    if ((int)$baris['sudah_dibaca'] === 0) {
        $jumlah_belum_dibaca++;
    }
}

mysqli_stmt_close($stmt);



$stmt = mysqli_prepare(
    $koneksi,
    "SELECT
        COALESCE(SUM(berat), 0) AS total_sampah,
        COALESCE(SUM(poin), 0) AS total_poin
     FROM setoran
     WHERE username = ?"
);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$total_sampah = (float)$data['total_sampah'];
$total_poin = (float)$data['total_poin'];
$reward_didapat = $total_poin * 100;
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

    <title>Dashboard - PilahKu+</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f0f8f0;
            background-image: url("images/background daun.jpg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
            color: #333;
        }

        .header {
            background: #2e7d32;
            color: white;
            padding: 20px;
        }

        .header-content {
            max-width: 1000px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

       .logo {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 28px;
    font-weight: bold;
}

.logo img {
    width: 70px;
    height: 70px;
    object-fit: contain;
}

        .header-text {
            text-align: right;
            font-size: 14px;
        }
        
.area-notifikasi {
    display: flex;
    align-items: center;
    gap: 12px;
}

.notifikasi-menu {
    position: relative;
}

.notifikasi-menu > summary {
    list-style: none;
}

.notifikasi-menu > summary::-webkit-details-marker {
    display: none;
}

.daftar-notifikasi {
    position: absolute;
    top: 50px;
    right: 0;
    width: 280px;
    max-width: 85vw;
    z-index: 1000;
}


.notifikasi {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border: 1px solid rgba(255,255,255,0.6);
    border-radius: 50%;
    color: white;
    text-decoration: none;
    font-size: 22px;
    cursor: pointer;
}

.jumlah-notifikasi {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff5252;
    color: white;
    border-radius: 50%;
    min-width: 19px;
    height: 19px;
    padding: 2px 5px;
    font-size: 11px;
    font-weight: bold;
    text-align: center;
}

.daftar-notifikasi {
    background: white;
    color: #333;
    border-radius: 12px;
    padding: 15px;
    margin-top: 15px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.08);
}

.item-notifikasi {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.item-notifikasi:last-child {
    border-bottom: none;
}

.notifikasi-kosong {
    color: #777;
    font-size: 14px;
}
.bar-notifikasi {
    background: white;
    border-left: 5px solid #2e7d32;
    padding: 15px 20px;
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.06);
}

.bar-notifikasi h3 {
    margin: 0 0 10px;
    color: #2e7d32;
    font-size: 16px;
}

.bar-notifikasi p {
    margin: 6px 0;
    color: #444;
    font-size: 14px;
}


        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
        }

        .sapaan {
            background: white;
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 25px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
        }

        .sapaan h2 {
            margin-top: 0;
            color: #2e7d32;
        }

        .sapaan p {
            color: #666;
            line-height: 1.6;
        }

        .kartu-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .kartu {
    background: white;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    padding: 25px;
    border-radius: 18px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.06);
}

        .ikon {
            font-size: 30px;
        }

        .kartu h3 {
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .nilai {
            font-size: 30px;
            font-weight: bold;
            margin: 10px 0;
        }

        .kartu p {
            color: #777;
            font-size: 14px;
        }

        .aksi {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .tombol {
            display: inline-block;
            padding: 13px 25px;
            background: #2e7d32;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
        }

        .tombol:hover {
            background: #1b5e20;
        }

        .tombol-kedua {
            background: white;
            color: #2e7d32;
            border: 2px solid #2e7d32;
        }

        .tombol-kedua:hover {
            background: #e8f5e9;
        }

        @media (max-width: 700px) {
            .kartu-container {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
                gap: 10px;
            }

            .header-text {
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <div class="header">

        <div class="header-content">

            <div class="logo">
    <img src="images/logo%20pilahku.png" alt="Logo PilahKu+">
    <span>PilahKu+</span>
</div>

            <div class="area-notifikasi">

    <details class="notifikasi-menu" id="menuNotifikasi">
    <summary class="notifikasi" id="tombolNotifikasi">
            🔔
            <?php if ($jumlah_belum_dibaca > 0): ?>
    <span class="jumlah-notifikasi" id="jumlahNotifikasi">
        <?php echo $jumlah_belum_dibaca; ?>
    </span>
<?php endif; ?>
        </summary>

        <div class="daftar-notifikasi">
            <strong>Notifikasi Pengajuan</strong>

            <?php if (count($notifikasi) > 0): ?>
                <?php foreach ($notifikasi as $item): ?>
                    <div class="item-notifikasi">
                        <?php if ($item['status'] === 'DISETUJUI'): ?>
                            Pengajuan Rp<?php echo number_format($item['jumlah'], 0, ',', '.'); ?>
                            telah disetujui.
                        <?php else: ?>
                            Penukaran Rp<?php echo number_format($item['jumlah'], 0, ',', '.'); ?>
                            telah selesai.
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="notifikasi-kosong">
                    Belum ada notifikasi baru.
                </p>
            <?php endif; ?>
        </div>
    </details>


</div>

        </div>

    </div>

        <div class="container">

        <div class="sapaan">

            <h2>Halo! 👋</h2>

            <p>
                Selamat datang di PilahKu+.
                Yuk, mulai pilah sampah dan kumpulkan poinmu!
            </p>

        </div>

        <div class="kartu-container">

            <div class="kartu">

                <div class="ikon">♻️</div>

                <h3>Total Sampah</h3>

                <div class="nilai">
                    <?php echo $total_sampah; ?> kg
                </div>

                <p>
                    Total sampah yang sudah kamu setor.
                </p>

            </div>

            <div class="kartu">

                <div class="ikon">⭐</div>

                <h3>Total Poin</h3>

                <div class="nilai">
    <?php echo $total_poin; ?>
</div>

                <p>
                    Poin yang sudah kamu kumpulkan.
                </p>

            </div>

            <div class="kartu">

                <div class="ikon">💰</div>

                <h3>Reward</h3>

                <div class="nilai">
        Rp<?php echo number_format($reward, 0, ',', '.'); ?>

</div>

                <p>
                    Saldo reward yang tersedia.
                </p>

            </div>

        </div>

        <div class="aksi">

            <a href="setor.php" class="tombol">
                ♻️ Setor Sampah
            </a>

            <a href="tukar.php" class="tombol tombol-kedua">
                💰 Tukar Reward
            </a>

            <a href="riwayat.php" class="tombol">📋 Riwayat Setoran
        
            </a>
        </div>

    </div>

    <script>
const menuNotifikasi = document.getElementById('menuNotifikasi');
const jumlahNotifikasi = document.getElementById('jumlahNotifikasi');

menuNotifikasi.addEventListener('toggle', async function () {
    if (menuNotifikasi.open && jumlahNotifikasi) {
        try {
            const respons = await fetch('baca_notifikasi.php', {
                method: 'POST',
                credentials: 'same-origin'
            });

            if (respons.ok) {
                jumlahNotifikasi.remove();
            }
        } catch (error) {
            console.error('Notifikasi gagal ditandai dibaca.');
        }
    }
});
</script>

</body>
</html>