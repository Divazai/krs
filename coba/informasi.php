<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php?pesan=belum_login");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Syarat Pengisian KRS</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ccc;
        }

        h2 {
            color: #2c3e50;
            text-align: center;
            margin-top: 0;
            margin-bottom: 10px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #e67e22;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .info-card {
            background: #ecf0f1;
            padding: 20px;
            border-radius: 5px;
            border-left: 5px solid #3498db;
            margin-bottom: 25px;
        }

        .info-card h3 {
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .info-card ul,
        .info-card p {
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }

        .info-card li {
            margin-bottom: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }

        .btn-kembali {
            display: inline-block;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            background-color: #3498db;
            color: white;
        }

        .btn-kembali:hover {
            background-color: #2980b9;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Syarat Pengisian KRS</h2>
        <div class="subtitle">Pastikan semua persyaratan berikut terpenuhi</div>

        <div class="info-card">
            <h3>1. Syarat Administratif & Akademik</h3>
            <ul>
                <li><strong>Status Aktif:</strong> Kamu harus terdaftar sebagai mahasiswa aktif pada semester berjalan.</li>
                <li><strong>Pelunasan UKT/SPP:</strong> Sudah membayar biaya kuliah atau tagihan administratif lainnya untuk membuka akses pengisian di sistem akademik (SI-UKT/Portal BIMA).</li>
                <li><strong>Batas SKS (Indeks Prestasi):</strong> Jumlah beban mata kuliah yang bisa kamu ambil bergantung pada nilai Indeks Prestasi Semester (IPS) sebelumnya. Semakin tinggi IP-mu, semakin banyak SKS yang bisa diambil (maksimal biasanya 24 SKS).</li>
                <li><strong>Dokumen Pendukung (Khusus Maba):</strong> Mahasiswa baru biasanya diminta melengkapi data biodata dan mengunggah dokumen seperti ijazah, KK, atau akta kelahiran sebelum bisa mengisi KRS.</li>
            </ul>
        </div>

        <div class="footer">
            <a href="isi_krs.php" class="btn-kembali">Pilih KRS</a>
        </div>
    </div>

</body>

</html>