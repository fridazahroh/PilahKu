<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Setor Sampah - PilahKu+</title>

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
            margin: 50px auto;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            color: #2e7d32;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        select,
        input {
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

        <h1>♻️ Setor Sampah</h1>

        <form action="konfirmasi.php" method="post">

            <label>Jenis Sampah</label>

            <select name="jenis" required>
                <option value="">Pilih jenis sampah</option>
                <option value="plastik">Plastik</option>
                <option value="kertas">Kertas</option>
                <option value="kaleng">Kaleng</option>
                <option value="lainnya">Lainnya</option>
            </select>

            <label>Berat Sampah (kg)</label>

            <input
                type="number"
                name="berat"
                step="0.1"
                min="0.1"
                placeholder="Contoh: 2.5"
                required
            >

            <button type="submit">
                Kirim Setoran
            </button>

        </form>

        <a href="dashboard.php" class="kembali">
            ← Kembali ke Dashboard
        </a>

    </div>

</body>
</html>