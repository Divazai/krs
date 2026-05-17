<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php?pesan=belum_login");
    exit;
}

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'krs';
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$nim = $_SESSION['nim'];
$query_mhs = "SELECT semester FROM mahasiswa WHERE nim = '$nim'";
$res_mhs = mysqli_query($conn, $query_mhs);
$mhs = mysqli_fetch_assoc($res_mhs);
$semester_mhs = $mhs ? $mhs['semester'] : 1;

$query_cek_krs = "SELECT COUNT(*) as jml FROM krs k 
                  JOIN mahasiswa m ON k.id_mahasiswa = m.id_mahasiswa 
                  WHERE m.nim = '$nim' AND k.semester_ambil = $semester_mhs AND k.status = 'aktif'";
$res_cek = mysqli_query($conn, $query_cek_krs);
$data_cek = mysqli_fetch_assoc($res_cek);
$sudah_mengisi = ($data_cek && $data_cek['jml'] > 0);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard KRS</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Baloo 2', cursive;
            background-color: #fefae0;
            background-image: linear-gradient(#e9edc9 1px, transparent 1px), linear-gradient(90deg, #e9edc9 1px, transparent 1px);
            background-size: 20px 20px;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            max-width: 550px;
            background: #ffffff;
            padding: 50px 40px 40px 40px;
            position: relative;
            box-shadow: 5px 5px 15px rgba(0,0,0,0.1);
            transform: rotate(1deg);
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .push-pin {
            width: 24px;
            height: 24px;
            background: #e63946;
            border-radius: 50%;
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 2px 2px 5px rgba(0,0,0,0.3);
            z-index: 10;
        }
        .push-pin::after {
            content: "";
            width: 8px;
            height: 8px;
            background: rgba(255,255,255,0.4);
            border-radius: 50%;
            position: absolute;
            top: 4px;
            left: 4px;
        }
        h2 {
            color: #606c38;
            font-weight: 800;
            text-align: center;
            margin-top: 0;
            margin-bottom: 25px;
            border-bottom: 2px dashed #ccd5ae;
            padding-bottom: 10px;
        }
        .sticky-note {
            background: #faedcd;
            padding: 25px;
            transform: rotate(-2deg);
            box-shadow: 3px 3px 10px rgba(0,0,0,0.1);
            border-left: 5px solid #dda15e;
            margin: 20px 0;
            position: relative;
            border-radius: 6px;
        }
        .sticky-note::before {
            content: "";
            width: 16px;
            height: 16px;
            background: #2a9d8f;
            border-radius: 50%;
            position: absolute;
            top: 5px;
            right: 5px;
        }
        .user-info p {
            margin: 5px 0;
            font-size: 20px;
            color: #283618;
            font-weight: 600;
        }
        .nim-badge {
            display: inline-block;
            background: #dda15e;
            color: #fff;
            padding: 2px 12px;
            border-radius: 20px;
            font-weight: 800;
            margin-top: 5px;
            font-size: 16px;
        }
        .alert {
            padding: 10px 15px;
            border-radius: 8px;
            margin: 15px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }
        .alert-success {
            background: #d4edda;
            border-left: 5px solid #2a9d8f;
            color: #155724;
        }
        .alert-warning {
            background: #fef9e6;
            border-left: 5px solid #dda15e;
            color: #856404;
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
            font-weight: 800;
            font-size: 18px;
            border-radius: 50px;
            text-align: center;
            transition: all 0.2s;
            text-transform: uppercase;
        }
        .btn-info {
            background: #2a9d8f;
            color: #fff;
            box-shadow: 3px 3px 0px #1e6b5e;
        }
        .btn-info:hover {
            background: #3eb9a9;
            transform: translateY(-2px);
            box-shadow: 5px 5px 0px #1e6b5e;
        }
        .btn-info:active {
            transform: translateY(2px);
            box-shadow: 1px 1px 0px #1e6b5e;
        }
        .btn-logout {
            background: #e63946;
            color: #fff;
            box-shadow: 3px 3px 0px #9b2226;
        }
        .btn-logout:hover {
            background: #f0505c;
            transform: translateY(-2px);
            box-shadow: 5px 5px 0px #9b2226;
        }
        .btn-logout:active {
            transform: translateY(2px);
            box-shadow: 1px 1px 0px #9b2226;
        }
        @media (max-width: 480px) {
            .container { padding: 40px 25px 30px 25px; transform: rotate(0deg); }
            .sticky-note { transform: rotate(0deg); }
            .btn-group { flex-direction: column; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="push-pin"></div>
    <h2>Halo, Mahasiswa! 🎒</h2>
    
    <div class="sticky-note">
        <div class="user-info">
            <p>Nama: <?= htmlspecialchars($_SESSION['nama']); ?></p>
            <div class="nim-badge">NIM: <?= htmlspecialchars($_SESSION['nim']); ?></div>
        </div>
    </div>

    <?php if ($sudah_mengisi): ?>
        <div class="alert alert-success">
            <span style="font-size:24px;">✅</span>
            <div><strong>Anda sudah mengisi KRS Semester <?= $semester_mhs ?>.</strong></div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <span style="font-size:24px;">⚠️</span>
            <div><strong>Anda belum mengisi KRS untuk Semester <?= $semester_mhs ?>.</strong></div>
        </div>
    <?php endif; ?>

    <div class="btn-group">
        <a href="informasi.php" class="btn btn-info">📋 Lihat Informasi</a>
        <?php if ($sudah_mengisi): ?>
            <a href="krs_saya.php" class="btn btn-info" style="background:#dda15e; box-shadow:3px 3px 0px #9c6644;">📖 KRS Saya</a>
        <?php endif; ?>
        <a href="logout.php" class="btn btn-logout">🚪 Keluar (Logout)</a>
    </div>
</div>
</body>
</html>