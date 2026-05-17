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
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

$nim = $_SESSION['nim'];

$cek_kolom = mysqli_query($conn, "SHOW COLUMNS FROM mahasiswa LIKE 'kunci_krs'");
if (mysqli_num_rows($cek_kolom) == 0) {
    mysqli_query($conn, "ALTER TABLE mahasiswa ADD COLUMN kunci_krs TINYINT(1) NOT NULL DEFAULT 0");
}

$query_mhs = "SELECT id_mahasiswa, nama, semester, ipk, kunci_krs FROM mahasiswa WHERE nim = '$nim'";
$res_mhs = mysqli_query($conn, $query_mhs);
if (!$res_mhs) die("Query error: " . mysqli_error($conn));
$mhs = mysqli_fetch_assoc($res_mhs);
if (!$mhs) die("Data mahasiswa tidak ditemukan.");

$id_mahasiswa = $mhs['id_mahasiswa'];
$nama = $mhs['nama'];
$semester_mhs = $mhs['semester'];
$ipk = $mhs['ipk'];
$is_locked = ($mhs['kunci_krs'] == 1);

// ========== SESUAIKAN BATAS SKS DI SINI ==========
if ($ipk >= 3.0) {
    $max_sks = 24;
} elseif ($ipk >= 2.5) {
    $max_sks = 22;
} elseif ($ipk >= 2.0) {
    $max_sks = 20;
} elseif ($ipk >= 1.5) {
    $max_sks = 16;   // ubah jadi 15 atau 18 jika perlu
} else {
    $max_sks = 12;
}
// ================================================

// Ambil KRS yang sudah dipilih
$query_existing = "SELECT id_matkul FROM krs WHERE id_mahasiswa = $id_mahasiswa AND semester_ambil = $semester_mhs AND status = 'aktif'";
$res_existing = mysqli_query($conn, $query_existing);
$sudah_dipilih = [];
while ($row = mysqli_fetch_assoc($res_existing)) $sudah_dipilih[] = $row['id_matkul'];

$total_sks_dipilih = 0;
if (!empty($sudah_dipilih)) {
    $id_list = implode(',', $sudah_dipilih);
    $res_total = mysqli_query($conn, "SELECT SUM(sks) as total FROM mata_kuliah WHERE id_matkul IN ($id_list)");
    $total = mysqli_fetch_assoc($res_total);
    $total_sks_dipilih = $total['total'] ?? 0;
}
$sisa_sks = $max_sks - $total_sks_dipilih;

// Proses simpan jika belum dikunci
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pilih_mk']) && !$is_locked) {
    $selected = $_POST['mk'] ?? [];
    if (empty($selected)) {
        $error = "Minimal pilih satu mata kuliah.";
    } else {
        $id_list = implode(',', $selected);
        $res_detail = mysqli_query($conn, "SELECT id_matkul, sks, kuota, terisi FROM mata_kuliah WHERE id_matkul IN ($id_list) AND status = 'aktif'");
        $mk_data = [];
        $total_sks_baru = 0;
        $kuota_penuh = false;
        while ($row = mysqli_fetch_assoc($res_detail)) {
            if ($row['terisi'] >= $row['kuota']) $kuota_penuh = true;
            $mk_data[$row['id_matkul']] = $row['sks'];
            $total_sks_baru += $row['sks'];
        }
        if ($kuota_penuh) $error = "Mata kuliah penuh.";
        elseif ($total_sks_baru > $max_sks) $error = "Total SKS melebihi batas maksimal $max_sks.";
        else {
            mysqli_begin_transaction($conn);
            try {
                foreach ($sudah_dipilih as $id_lama) {
                    mysqli_query($conn, "UPDATE mata_kuliah SET terisi = GREATEST(terisi - 1, 0) WHERE id_matkul = $id_lama");
                }
                mysqli_query($conn, "DELETE FROM krs WHERE id_mahasiswa = $id_mahasiswa AND semester_ambil = $semester_mhs");
                foreach ($selected as $id_matkul) {
                    $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT kuota, terisi FROM mata_kuliah WHERE id_matkul = $id_matkul"));
                    if ($check['terisi'] >= $check['kuota']) throw new Exception("Kuota penuh");
                    mysqli_query($conn, "INSERT INTO krs (id_mahasiswa, id_matkul, semester_ambil, status) VALUES ($id_mahasiswa, $id_matkul, $semester_mhs, 'aktif')");
                    mysqli_query($conn, "UPDATE mata_kuliah SET terisi = terisi + 1 WHERE id_matkul = $id_matkul");
                }
                mysqli_commit($conn);
                $_SESSION['message'] = "KRS berhasil disimpan! Total SKS: $total_sks_baru";
                header("Location: isi_krs.php");
                exit;
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error = $e->getMessage();
            }
        }
    }
    // refresh data setelah error
    $res_existing = mysqli_query($conn, $query_existing);
    $sudah_dipilih = [];
    while ($row = mysqli_fetch_assoc($res_existing)) $sudah_dipilih[] = $row['id_matkul'];
}

// Daftar mata kuliah
$query_mk = "SELECT m.*, d.nama_dosen, (m.kuota - m.terisi) as sisa_kuota 
             FROM mata_kuliah m 
             LEFT JOIN dosen d ON m.id_dosen = d.id_dosen
             WHERE m.semester = $semester_mhs AND m.status = 'aktif'
             ORDER BY m.kode_matkul";
$res_mk = mysqli_query($conn, $query_mk);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pilih KRS</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Baloo 2', cursive; background: #fefae0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #606c38; }
        .info { background: #faedcd; padding: 10px; border-left: 5px solid #2a9d8f; margin-bottom: 20px; display: flex; gap: 20px; flex-wrap: wrap; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #ccd5ae; }
        .btn { padding: 10px 20px; border-radius: 30px; text-decoration: none; display: inline-block; margin-top: 15px; margin-right: 10px; }
        .btn-primary { background: #2a9d8f; color: white; }
        .btn-secondary { background: #dda15e; color: white; }
        .error { background: #f8d7da; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .success { background: #d4edda; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .alert-warning { background: #fff3cd; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
    </style>
</head>
<body>
<div class="container">
    <h2>📋 Pilih Mata Kuliah - Semester <?= $semester_mhs ?></h2>
    <div class="info">
        <span>👤 <?= htmlspecialchars($nama) ?> (<?= $nim ?>)</span>
        <span>🏅 IPK: <?= number_format($ipk,2) ?></span>
        <span>🎯 Maks SKS: <?= $max_sks ?></span>
        <span>✅ SKS terpilih: <?= $total_sks_dipilih ?></span>
        <span>⚡ Sisa SKS: <?= $sisa_sks ?></span>
    </div>

    <?php if ($is_locked): ?>
        <div class="alert-warning">🔒 KRS sudah dikunci. Anda tidak dapat mengubah pilihan.</div>
    <?php endif; ?>

    <?php if (isset($error)): ?><div class="error">⚠️ <?= $error ?></div><?php endif; ?>
    <?php if (isset($_SESSION['message'])): ?><div class="success">✅ <?= $_SESSION['message']; unset($_SESSION['message']); ?></div><?php endif; ?>

    <form method="post">
        <table>
            <thead><tr><th>Pilih</th><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Dosen</th><th>Kuota</th></tr></thead>
            <tbody>
                <?php while ($mk = mysqli_fetch_assoc($res_mk)): 
                    $checked = in_array($mk['id_matkul'], $sudah_dipilih);
                    $disabled = ($is_locked || ($sisa_sks <= 0 && !$checked) || $mk['sisa_kuota'] <= 0);
                ?>
                <tr>
                    <td><input type="checkbox" name="mk[]" value="<?= $mk['id_matkul'] ?>" <?= $checked ? 'checked' : '' ?> <?= $disabled ? 'disabled' : '' ?>></td>
                    <td><?= $mk['kode_matkul'] ?></td>
                    <td><?= $mk['nama_matkul'] ?></td>
                    <td><?= $mk['sks'] ?></td>
                    <td><?= htmlspecialchars($mk['nama_dosen'] ?? '-') ?></td>
                    <td><?= $mk['terisi'] ?>/<?= $mk['kuota'] ?> (sisa <?= $mk['sisa_kuota'] ?>)</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php if (!$is_locked): ?>
            <button type="submit" name="pilih_mk" class="btn btn-primary">💾 Simpan KRS</button>
        <?php endif; ?>
        <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
        <a href="informasi.php" class="btn btn-secondary" style="background:#dda15e;">📋 Lihat Syarat</a>
        <?php if ($total_sks_dipilih > 0): ?>
            <a href="krs_saya.php" class="btn btn-primary" style="background:#2a9d8f;">📖 Lihat KRS Saya</a>
        <?php endif; ?>
    </form>
</div>
</body>
</html>