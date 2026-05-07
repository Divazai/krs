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
if (!$conn) die("Koneksi gagal");

$nim = $_SESSION['nim'];
$query_mhs = "SELECT id_mahasiswa, nama, semester, kunci_krs FROM mahasiswa WHERE nim = '$nim'";
$res_mhs = mysqli_query($conn, $query_mhs);
$mhs = mysqli_fetch_assoc($res_mhs);
if (!$mhs) die("Data tidak ditemukan");
$id_mahasiswa = $mhs['id_mahasiswa'];
$nama = $mhs['nama'];
$semester_mhs = $mhs['semester'];
$is_locked = ($mhs['kunci_krs'] == 1);

// Proses kunci pilihan
if (isset($_GET['kunci']) && !$is_locked) {
    mysqli_query($conn, "UPDATE mahasiswa SET kunci_krs = 1 WHERE nim = '$nim'");
    $_SESSION['message'] = "KRS berhasil dikunci. Anda tidak dapat mengubah pilihan lagi.";
    header("Location: krs_saya.php");
    exit;
}

// Proses batal (hanya jika belum dikunci)
if (isset($_GET['batal']) && is_numeric($_GET['batal']) && !$is_locked) {
    $id_matkul_batal = (int)$_GET['batal'];
    $cek = mysqli_query($conn, "SELECT id_krs FROM krs WHERE id_mahasiswa = $id_mahasiswa AND id_matkul = $id_matkul_batal AND semester_ambil = $semester_mhs AND status = 'aktif'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_begin_transaction($conn);
        $hapus = mysqli_query($conn, "DELETE FROM krs WHERE id_mahasiswa = $id_mahasiswa AND id_matkul = $id_matkul_batal AND semester_ambil = $semester_mhs");
        if ($hapus) {
            mysqli_query($conn, "UPDATE mata_kuliah SET terisi = terisi - 1 WHERE id_matkul = $id_matkul_batal");
            mysqli_commit($conn);
            $_SESSION['message'] = "Mata kuliah berhasil dibatalkan.";
        } else {
            mysqli_rollback($conn);
            $_SESSION['error'] = "Gagal membatalkan.";
        }
    } else {
        $_SESSION['error'] = "Mata kuliah tidak ditemukan.";
    }
    header("Location: krs_saya.php");
    exit;
}

// Ambil daftar KRS
$query_krs = "SELECT k.id_matkul, mk.kode_matkul, mk.nama_matkul, mk.sks, 
                     d.nama_dosen, d.gelar, j.hari, j.jam_mulai, j.jam_selesai, j.ruangan
              FROM krs k
              JOIN mata_kuliah mk ON k.id_matkul = mk.id_matkul
              LEFT JOIN dosen d ON mk.id_dosen = d.id_dosen
              LEFT JOIN jadwal j ON mk.id_matkul = j.id_matkul
              WHERE k.id_mahasiswa = $id_mahasiswa AND k.semester_ambil = $semester_mhs AND k.status = 'aktif'
              ORDER BY j.hari, j.jam_mulai";
$res_krs = mysqli_query($conn, $query_krs);
$krs_list = [];
$total_sks = 0;
while ($row = mysqli_fetch_assoc($res_krs)) {
    $total_sks += $row['sks'];
    $krs_list[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>KRS Saya</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Baloo 2', cursive; background: #fefae0; padding: 20px; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        h2 { color: #606c38; }
        .info-mhs { background: #faedcd; padding: 10px; border-radius: 8px; display: flex; justify-content: space-between; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #ccd5ae; }
        .btn-batal, .btn-kunci, .btn-back, .btn-cetak { padding: 5px 12px; border-radius: 20px; text-decoration: none; display: inline-block; margin-right: 5px; }
        .btn-batal { background: #e63946; color: white; }
        .btn-kunci { background: #2a9d8f; color: white; }
        .btn-back { background: #dda15e; color: white; }
        .btn-cetak { background: #606c38; color: white; }
        .kosong { text-align: center; padding: 30px; color: #bc6c25; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .alert-success { background: #d4edda; }
        .alert-error { background: #f8d7da; }
        @media print { .aksi, .btn-kunci, .btn-back, .btn-cetak, .no-print { display: none; } }
    </style>
</head>
<body>
<div class="container">
    <h2>📘 Kartu Rencana Studi - Semester <?= $semester_mhs ?></h2>
    <div class="info-mhs">
        <span>👤 <?= htmlspecialchars($nama) ?> (<?= $nim ?>)</span>
        <span>📖 Total SKS: <?= $total_sks ?></span>
        <?php if ($is_locked): ?>
            <span>🔒 Status: <strong>TERKUNCI</strong></span>
        <?php else: ?>
            <span>🔓 Status: <strong>Belum dikunci</strong></span>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">✅ <?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">⚠️ <?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (count($krs_list) > 0): ?>
        <table>
            <thead>
                <tr><th>No</th><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Dosen</th><th>Jadwal</th><th>Ruangan</th><?php if (!$is_locked): ?><th class="aksi">Aksi</th><?php endif; ?></tr>
            </thead>
            <tbody>
                <?php $no=1; foreach ($krs_list as $krs): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $krs['kode_matkul'] ?></td>
                    <td><?= $krs['nama_matkul'] ?></td>
                    <td><?= $krs['sks'] ?></td>
                    <td><?= htmlspecialchars($krs['nama_dosen'] ?? '-') ?></td>
                    <td><?= $krs['hari'] ? $krs['hari'].' '.date('H:i',strtotime($krs['jam_mulai'])).'-'.date('H:i',strtotime($krs['jam_selesai'])) : '-' ?></td>
                    <td><?= $krs['ruangan'] ?? '-' ?></td>
                    <?php if (!$is_locked): ?>
                    <td class="aksi"><a href="?batal=<?= $krs['id_matkul'] ?>" class="btn-batal" onclick="return confirm('Batalkan?')">Batal</a></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total-sks" style="font-weight:bold; margin-top:10px;">Total SKS: <?= $total_sks ?></div>
        
        <?php if (!$is_locked): ?>
            <div style="margin: 20px 0;">
                <a href="?kunci=1" class="btn-kunci" onclick="return confirm('Setelah dikunci, Anda tidak bisa mengubah KRS lagi. Lanjutkan?')">🔒 Kunci Pilihan</a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="kosong">🧾 Belum ada mata kuliah. <a href="isi_krs.php">Isi KRS</a></div>
    <?php endif; ?>

    <div class="no-print">
        <a href="dashboard.php" class="btn-back">← Dashboard</a>
        <a href="isi_krs.php" class="btn-back" style="background:#2a9d8f;">✏️ Edit KRS</a>
        <button onclick="window.print()" class="btn-cetak">🖨️ Cetak</button>
    </div>
</div>
</body>
</html>