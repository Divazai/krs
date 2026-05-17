<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php?pesan=belum_login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat Pengisian KRS</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Baloo 2', cursive;
            background-color: #fefae0;
            background-image: 
                linear-gradient(#e9edc9 1px, transparent 1px),
                linear-gradient(90deg, #e9edc9 1px, transparent 1px);
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
            max-width: 700px;
            background: #ffffff;
            padding: 40px 35px;
            position: relative;
            box-shadow: 5px 5px 15px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
            transform: rotate(-0.5deg);
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
            margin-bottom: 10px;
            border-bottom: 2px dashed #ccd5ae;
            padding-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            color: #bc6c25;
            font-weight: 600;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        .info-card {
            background: #faedcd;
            padding: 20px 25px;
            transform: rotate(1deg);
            box-shadow: 3px 3px 10px rgba(0,0,0,0.1);
            border-left: 8px solid #2a9d8f;
            margin-bottom: 30px;
            border-radius: 6px;
        }
        .info-card h3 {
            color: #283618;
            font-weight: 800;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-card ul, .info-card p {
            font-size: 1.1rem;
            line-height: 1.5;
            color: #2f3e46;
        }
        .info-card li {
            margin-bottom: 12px;
        }
        .btn-kembali {
            display: inline-block;
            text-decoration: none;
            padding: 10px 25px;
            border-radius: 40px;
            font-weight: bold;
            font-size: 1rem;
            transition: all 0.2s;
            text-align: center;
            background: #2a9d8f;
            color: white;
            box-shadow: 2px 2px 0px #1e6b5e;
        }
        .btn-kembali:hover {
            background: #3eb9a9;
            transform: translateY(-2px);
            box-shadow: 4px 4px 0px #1e6b5e;
        }
        .btn-kembali:active {
            transform: translateY(2px);
            box-shadow: 1px 1px 0px #1e6b5e;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
        @media (max-width: 550px) {
            .container { padding: 30px 20px; transform: rotate(0deg); }
            .info-card { transform: rotate(0deg); }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="push-pin"></div>
    <h2> Syarat Pengisian KRS</h2>
    <div class="subtitle">Pastikan semua persyaratan berikut terpenuhi</div>

    <div class="info-card">
        <h3> 1. Syarat Administratif & Akademik</h3>
        <ul>
            <li><strong>Status Aktif:</strong> Kamu harus terdaftar sebagai mahasiswa aktif pada semester berjalan.</li>
            <li><strong>Pelunasan UKT/SPP:</strong> Sudah membayar biaya kuliah atau tagihan administratif lainnya untuk membuka akses pengisian di sistem akademik (SIAKAD/Portal Kampus).</li>
            <li><strong>Batas SKS (Indeks Prestasi):</strong> Jumlah beban mata kuliah yang bisa kamu ambil bergantung pada nilai Indeks Prestasi Semester (IPS) sebelumnya. Semakin tinggi IP-mu, semakin banyak SKS yang bisa diambil (maksimal biasanya 24 SKS).</li>
            <li><strong>Dokumen Pendukung (Khusus Maba):</strong> Mahasiswa baru biasanya diminta melengkapi data biodata dan mengunggah dokumen seperti ijazah, KK, atau akta kelahiran sebelum bisa mengisi KRS.</li>
        </ul>
    </div>

    <div class="footer">
        <a href="isi_krs.php" class="btn-kembali"> Pilih KRS</a>
    </div>
</div>

</body>
</html>