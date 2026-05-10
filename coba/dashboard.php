<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php?pesan=belum_login");
    exit;
}

$nim = $_SESSION['nim'];
$query_mhs = "SELECT semester FROM mahasiswa WHERE nim = '$nim'";
$res_mhs = mysqli_query($konek, $query_mhs);
$mhs = mysqli_fetch_assoc($res_mhs);
$semester_mhs = $mhs ? $mhs['semester'] : 1;

$query_cek_krs = "SELECT COUNT(*) as jml FROM krs k 
                  JOIN mahasiswa m ON k.id_mahasiswa = m.id_mahasiswa 
                  WHERE m.nim = '$nim' AND k.semester_ambil = $semester_mhs AND k.status = 'aktif'";
$res_cek = mysqli_query($konek, $query_cek_krs);
$data_cek = mysqli_fetch_assoc($res_cek);
$sudah_mengisi = ($data_cek && $data_cek['jml'] > 0);

mysqli_close($konek);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Dashboard KRS</title>
    <style>
        /* CSS sederhana seperti Modul 2 */
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ccc;
        }

        h2 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .info p {
            margin: 8px 0;
            font-size: 16px;
            display: flex;
            gap: 5px;
        }

        .info p strong {
            min-width: 100px;
            /* Atur lebar minimum untuk label */
            display: inline-block;
        }

        .info p span {
            display: inline-block;
        }

        .info {
            background: #ecf0f1;
            padding: 20px;
        }

        .alert {
            padding: 12px;
            border-radius: 5px;
            margin: 15px 0;
            text-align: center;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn {
            flex: 1;
            text-decoration: none;
            padding: 12px;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn-warning {
            background-color: #f3c212;
            color: white;
        }

        .btn-warning:hover {
            background-color: #d39e00;
        }

        .btn-info {
            background-color: #3498db;
            color: white;
        }

        .btn-info:hover {
            background-color: #2980b9;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Dashboard KRS</h2>

        <div class="info">
            <p>
                <strong>Nama</strong>
                <span>:</span>
                <span><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
            </p>
            <p>
                <strong>NIM</strong>
                <span>:</span>
                <span><?php echo htmlspecialchars($_SESSION['nim']); ?></span>
            </p>
            <p>
                <strong>Semester</strong>
                <span>:</span>
                <span><?php echo $semester_mhs; ?></span>
            </p>
        </div>

        <?php if ($sudah_mengisi): ?>
            <div class="alert alert-success">
                Anda sudah mengisi KRS untuk Semester <?php echo $semester_mhs; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                Anda belum mengisi KRS untuk Semester <?php echo $semester_mhs; ?>
            </div>
        <?php endif; ?>

        <div class="btn-group">
            <a href="informasi.php" class="btn btn-info">Lihat Informasi</a>
            <?php if ($sudah_mengisi): ?>
                <a href="krs_saya.php" class="btn btn-warning">KRS Saya</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</body>

</html>