<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php?pesan=belum_login");
    exit;
}

// Koneksi database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'krs';
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$nim = $_SESSION['nim'];

// Ambil data mahasiswa
$query_mhs = "SELECT id_mahasiswa, nama, semester FROM mahasiswa WHERE nim = '$nim'";
$res_mhs = mysqli_query($conn, $query_mhs);
$mhs = mysqli_fetch_assoc($res_mhs);
if (!$mhs) {
    die("Data mahasiswa tidak ditemukan.");
}
$id_mahasiswa = $mhs['id_mahasiswa'];
$nama = $mhs['nama'];
$semester_mhs = $mhs['semester'];

// Proses pembatalan mata kuliah
if (isset($_GET['batal']) && is_numeric($_GET['batal'])) {
    $id_matkul_batal = (int)$_GET['batal'];
    // Cek apakah mata kuliah tersebut memang diambil oleh mahasiswa ini
    $cek = mysqli_query($conn, "SELECT id_krs FROM krs WHERE id_mahasiswa = $id_mahasiswa AND id_matkul = $id_matkul_batal AND semester_ambil = $semester_mhs AND status = 'aktif'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_begin_transaction($conn);
        $hapus = mysqli_query($conn, "DELETE FROM krs WHERE id_mahasiswa = $id_mahasiswa AND id_matkul = $id_matkul_batal AND semester_ambil = $semester_mhs");
        if ($hapus) {
            mysqli_query($conn, "UPDATE mata_kuliah SET terisi = terisi - 1 WHERE id_matkul = $id_matkul_batal");
            mysqli_commit($conn);
            $_SESSION['message'] = "Berhasil membatalkan mata kuliah.";
        } else {
            mysqli_rollback($conn);
            $_SESSION['error'] = "Gagal membatalkan mata kuliah.";
        }
    } else {
        $_SESSION['error'] = "Mata kuliah tidak ditemukan dalam KRS Anda.";
    }
    header("Location: krs_saya.php");
    exit;
}

// Ambil daftar KRS mahasiswa beserta detail mata kuliah, jadwal, dosen
$query_krs = "SELECT k.id_krs, k.id_matkul, mk.kode_matkul, mk.nama_matkul, mk.sks, 
                     d.nama_dosen, d.gelar, j.hari, j.jam_mulai, j.jam_selesai, j.ruangan
              FROM krs k
              JOIN mata_kuliah mk ON k.id_matkul = mk.id_matkul
              LEFT JOIN dosen d ON mk.id_dosen = d.id_dosen
              LEFT JOIN jadwal j ON mk.id_matkul = j.id_matkul
              WHERE k.id_mahasiswa = $id_mahasiswa AND k.semester_ambil = $semester_mhs AND k.status = 'aktif'
              ORDER BY j.hari, j.jam_mulai";
$res_krs = mysqli_query($conn, $query_krs);

$total_sks = 0;
$krs_list = [];
while ($row = mysqli_fetch_assoc($res_krs)) {
    $total_sks += $row['sks'];
    $krs_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRS Saya - <?= htmlspecialchars($nama) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Baloo 2', cursive;
            background-color: #fefae0;
            background-image: linear-gradient(#e9edc9 1px, transparent 1px), linear-gradient(90deg, #e9edc9 1px, transparent 1px);
            background-size: 20px 20px;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            max-width: 1100px;
            width: 100%;
            background: white;
            padding: 25px;
            box-shadow: 5px 5px 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        h2 {
            color: #606c38;
            border-bottom: 2px dashed #ccd5ae;
            padding-bottom: 10px;
        }
        .info-mhs {
            background: #faedcd;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #ccd5ae;
            color: #283618;
        }
        .btn-batal {
            background-color: #e63946;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.8rem;
            text-decoration: none;
            display: inline-block;
        }
        .btn-batal:hover {
            background-color: #c1121f;
        }
        .btn-back, .btn-cetak {
            background: #2a9d8f;
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            margin-right: 10px;
        }
        .btn-back {
            background: #dda15e;
        }
        .total-sks {
            font-weight: bold;
            background: #e9edc9;
            padding: 8px;
            text-align: right;
            margin-top: 15px;
            border-radius: 5px;
        }
        .kosong {
            text-align: center;
            padding: 30px;
            color: #bc6c25;
            font-size: 1.2rem;
        }
        @media print {
            .btn-batal, .btn-back, .btn-cetak, .aksi {
                display: none;
            }
            body {
                background: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>📘 Kartu Rencana Studi (KRS) - Semester <?= $semester_mhs ?></h2>
    <div class="info-mhs">
        <span>👤 <?= htmlspecialchars($nama) ?> (<?= htmlspecialchars($nim) ?>)</span>
        <span>📅 Semester: <?= $semester_mhs ?></span>
        <span>📖 Total SKS: <?= $total_sks ?></span>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="success" style="background:#d4edda; padding:8px; border-radius:5px; margin-bottom:15px;">✅ <?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error" style="background:#f8d7da; padding:8px; border-radius:5px; margin-bottom:15px;">⚠️ <?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (count($krs_list) > 0): ?>
        <table>
            <thead>
                <tr><th>No</th><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Dosen</th><th>Jadwal</th><th>Ruangan</th><th class="aksi">Aksi</th></tr>
            </thead>
            <tbody>
                <?php $no=1; foreach ($krs_list as $krs): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($krs['kode_matkul']) ?></td>
                    <td><?= htmlspecialchars($krs['nama_matkul']) ?></td>
                    <td><?= $krs['sks'] ?></td>
                    <td><?= htmlspecialchars($krs['nama_dosen'] ?? '-') ?> <?= htmlspecialchars($krs['gelar'] ?? '') ?></td>
                    <td>
                        <?php if ($krs['hari']): ?>
                            <?= $krs['hari'] ?>, <?= date('H:i', strtotime($krs['jam_mulai'])) ?> - <?= date('H:i', strtotime($krs['jam_selesai'])) ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($krs['ruangan'] ?? '-') ?></td>
                    <td class="aksi">
                        <a href="?batal=<?= $krs['id_matkul'] ?>" class="btn-batal" onclick="return confirm('Yakin ingin membatalkan mata kuliah ini?')">Batal</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total-sks">Total SKS yang diambil: <?= $total_sks ?> SKS</div>
    <?php else: ?>
        <div class="kosong">
            🧾 Belum ada mata kuliah yang diambil.<br>
            Silakan <a href="isi_krs.php" style="color:#2a9d8f;">isi KRS</a> terlebih dahulu.
        </div>
    <?php endif; ?>

    <div>
        <a href="dashboard.php" class="btn-back">← Dashboard</a>
        <a href="isi_krs.php" class="btn-back" style="background:#2a9d8f;">✏️ Edit KRS</a>
        <button onclick="window.print()" class="btn-cetak">🖨️ Cetak KRS</button>
    </div>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>