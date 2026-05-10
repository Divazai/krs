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
            <h3>1. Persyaratan Akademik</h3>
            <ul>
                <li><strong>Batas SKS (IPK):</strong> Jumlah SKS maksimal yang boleh diambil ditentukan oleh Indeks Prestasi Semester (IPS) sebelumnya. IPK tinggi (misal >3.00) umumnya bisa mengambil hingga 24 SKS, sedangkan IPK rendah dibatasi (misal 12–18 SKS).
                </li>
                <li><strong>Mata Kuliah Bersyarat:</strong> Pastikan Anda sudah lulus mata kuliah tingkat awal sebelum mengambil mata kuliah lanjutannya (contoh: wajib lulus Kalkulus 1 untuk mengambil Kalkulus 2).
                </li>
                <li><strong>Status UKT:</strong> Anda harus melunasi biaya kuliah semester berjalan (UKT/SPP) agar sistem KRS di portal akademik otomatis terbuka.</li>
            </ul>
            <h3>2. Aturan Pemilihan Jadwal</h3>
            <ul>
                <li><strong>Hindari Jadwal Bentrok:</strong> Sistem biasanya otomatis menolak jika Anda memilih dua mata kuliah di jam yang sama.
                </li>
                <li><strong>Kuota Kelas:</strong> Setiap kelas memiliki kapasitas terbatas. Jika kuota penuh, Anda harus memilih kelas/dosen lain, atau menunggu kebijakan pembukaan kelas baru dari jurusan.
                </li>
                <li><strong>Prioritas Angkatan:</strong> Beberapa kampus membuka akses KRS secara bertahap, mendahulukan mahasiswa angkatan tua (akhir) baru kemudian angkatan di bawahnya.
                </li>
            </ul>
        </div>

        <div class="footer">
            <a href="isi_krs.php" class="btn-kembali">Pilih KRS</a>
        </div>
    </div>

</body>

</html>