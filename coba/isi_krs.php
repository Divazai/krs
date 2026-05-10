<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['login'])) {
    header("Location: login.php?pesan=belum_login");
    exit;
}

$nim = $_SESSION['nim'];

$query_mhs = "SELECT id_mahasiswa, nama, semester FROM mahasiswa WHERE nim = '$nim'";
$res_mhs = mysqli_query($konek, $query_mhs);
if (!$res_mhs) die("Query error: " . mysqli_error($konek));
$mhs = mysqli_fetch_assoc($res_mhs);
if (!$mhs) die("Data mahasiswa tidak ditemukan.");

$id_mahasiswa = $mhs['id_mahasiswa'];
$nama = $mhs['nama'];
$semester_mhs = $mhs['semester'];

$max_sks = 20;

$query_existing = "SELECT id_matkul FROM krs WHERE id_mahasiswa = $id_mahasiswa AND semester_ambil = $semester_mhs AND status = 'aktif'";
$res_existing = mysqli_query($konek, $query_existing);
$sudah_dipilih = [];
while ($row = mysqli_fetch_assoc($res_existing)) {
    $sudah_dipilih[] = $row['id_matkul'];
}

$total_sks_dipilih = 0;
if (!empty($sudah_dipilih)) {
    $id_list = implode(',', $sudah_dipilih);
    $res_total = mysqli_query($konek, "SELECT SUM(sks) as total FROM mata_kuliah WHERE id_matkul IN ($id_list)");
    $total = mysqli_fetch_assoc($res_total);
    $total_sks_dipilih = $total['total'] ?? 0;
}
$sisa_sks = $max_sks - $total_sks_dipilih;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pilih_mk'])) {
    $selected = $_POST['mk'] ?? [];
    if (empty($selected)) {
        $error = "Minimal pilih satu mata kuliah.";
    } else {
        $id_list = implode(',', $selected);
        $res_sks = mysqli_query($konek, "SELECT id_matkul, sks FROM mata_kuliah WHERE id_matkul IN ($id_list)");
        $total_sks_baru = 0;
        while ($row = mysqli_fetch_assoc($res_sks)) {
            $total_sks_baru += $row['sks'];
        }
        if ($total_sks_baru > $max_sks) {
            $error = "Total SKS melebihi batas maksimal $max_sks.";
        } else {
            mysqli_query($konek, "DELETE FROM krs WHERE id_mahasiswa = $id_mahasiswa AND semester_ambil = $semester_mhs");
            foreach ($selected as $id_matkul) {
                mysqli_query($konek, "INSERT INTO krs (id_mahasiswa, id_matkul, semester_ambil, status) VALUES ($id_mahasiswa, $id_matkul, $semester_mhs, 'aktif')");
            }
            $_SESSION['message'] = "KRS berhasil disimpan! Total SKS: $total_sks_baru";
            header("Location: isi_krs.php");
            exit;
        }
    }
    $res_existing = mysqli_query($konek, $query_existing);
    $sudah_dipilih = [];
    while ($row = mysqli_fetch_assoc($res_existing)) $sudah_dipilih[] = $row['id_matkul'];
}

$query_mk = "SELECT m.*, d.nama_dosen 
             FROM mata_kuliah m 
             LEFT JOIN dosen d ON m.id_dosen = d.id_dosen
             WHERE m.semester = $semester_mhs AND m.status = 'aktif'
             ORDER BY m.kode_matkul";
$res_mk = mysqli_query($konek, $query_mk);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Pilih KRS</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ccc;
        }

        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .info {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            margin-right: 10px;
            font-weight: bold;
        }

        .btn-primary {
            background-color: #3498db;
            font-size: 15px;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .error {
            background: #f2dede;
            color: #a94442;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ebccd1;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Pilih Mata Kuliah - Semester <?php echo $semester_mhs; ?></h2>
        <div class="info">
            <span>Nama: <?php echo htmlspecialchars($nama); ?> (<?php echo htmlspecialchars($nim); ?>)</span>
            <span>Maks SKS: <?php echo $max_sks; ?></span>
            <span>SKS terpilih: <?php echo $total_sks_dipilih; ?></span>
            <span>Sisa SKS: <?php echo $sisa_sks; ?></span>
        </div>

        <?php if (isset($error)): ?>
            <div class="error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="success">✅ <?php echo $_SESSION['message'];
                                    unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <form method="post">
            <table>
                <thead>
                    <tr>
                        <th>Pilih</th>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Dosen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($mk = mysqli_fetch_assoc($res_mk)):
                        $checked = in_array($mk['id_matkul'], $sudah_dipilih);
                        $disabled = ($sisa_sks <= 0 && !$checked);
                    ?>
                        <tr>
                            <td><input type="checkbox" name="mk[]" value="<?php echo $mk['id_matkul']; ?>" <?php echo $checked ? 'checked' : ''; ?> <?php echo $disabled ? 'disabled' : ''; ?>></td>
                            <td><?php echo $mk['kode_matkul']; ?></td>
                            <td><?php echo $mk['nama_matkul']; ?></td>
                            <td><?php echo $mk['sks']; ?></td>
                            <td><?php echo htmlspecialchars($mk['nama_dosen'] ?? '-'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <button type="submit" name="pilih_mk" class="btn btn-primary">Simpan KRS</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
            <a href="krs_saya.php" class="btn btn-secondary">Lihat KRS Saya</a>
        </form>
    </div>
</body>

</html>
<?php mysqli_close($konek); ?>