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
$mhs = mysqli_fetch_assoc($res_mhs);
if (!$mhs) die("Data mahasiswa tidak ditemukan.");
$id_mahasiswa = $mhs['id_mahasiswa'];
$nama = $mhs['nama'];
$semester_mhs = $mhs['semester'];

if (isset($_GET['batal']) && is_numeric($_GET['batal'])) {
    $id_matkul_batal = (int)$_GET['batal'];
    $cek = mysqli_query($konek, "SELECT id_krs FROM krs WHERE id_mahasiswa = $id_mahasiswa AND id_matkul = $id_matkul_batal AND semester_ambil = $semester_mhs AND status = 'aktif'");
    if (mysqli_num_rows($cek) > 0) {
        $hapus = mysqli_query($konek, "DELETE FROM krs WHERE id_mahasiswa = $id_mahasiswa AND id_matkul = $id_matkul_batal AND semester_ambil = $semester_mhs");
        if ($hapus) {
            $_SESSION['message'] = "Mata kuliah berhasil dibatalkan.";
        } else {
            $_SESSION['error'] = "Gagal membatalkan mata kuliah.";
        }
    } else {
        $_SESSION['error'] = "Mata kuliah tidak ditemukan dalam KRS Anda.";
    }
    header("Location: krs_saya.php");
    exit;
}

$query_krs = "SELECT k.id_matkul, mk.kode_matkul, mk.nama_matkul, mk.sks, d.nama_dosen, d.gelar
              FROM krs k
              JOIN mata_kuliah mk ON k.id_matkul = mk.id_matkul
              LEFT JOIN dosen d ON mk.id_dosen = d.id_dosen
              WHERE k.id_mahasiswa = $id_mahasiswa AND k.semester_ambil = $semester_mhs AND k.status = 'aktif'
              ORDER BY mk.kode_matkul";
$res_krs = mysqli_query($konek, $query_krs);

$total_sks = 0;
$krs_list = [];
while ($row = mysqli_fetch_assoc($res_krs)) {
    $total_sks += $row['sks'];
    $krs_list[] = $row;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>KRS Saya</title>
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

        .info-mhs {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
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
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        .no-print {
            display: flex;
        }
        .btn-back,
        .btn-batal {
            background-color: #e74c3c;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 15px;
        }
        .btn-batal:hover {
            background-color: #c0392b;
        }

        .btn-back,
        .btn-edit {
            flex: 1;
            font-size: 15px;
            text-align: center;
            display: inline-block;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            margin-top: 15px;
            margin-right: 10px;
            font-weight: bold;
        }

        .btn-back {
            background-color: #95a5a6;
            color: white;
        }

        .btn-edit {
            background-color: #3498db;
            color: white;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        .kosong {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
        }

        .total-sks {
            font-weight: bold;
            margin-top: 15px;
            text-align: right;
        }

        @media print {

            .aksi,
            .btn-back,
            .btn-edit,
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Kartu Rencana Studi - Semester <?php echo $semester_mhs; ?></h2>
        <div class="info-mhs">
            <span>Nama: <?php echo htmlspecialchars($nama); ?> (<?php echo htmlspecialchars($nim); ?>)</span>
            <span>Total SKS: <?php echo $total_sks; ?></span>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['message'];
                unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"> <?php echo $_SESSION['error'];
            unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (count($krs_list) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Dosen</th>
                        <th class="aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($krs_list as $krs): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($krs['kode_matkul']); ?></td>
                            <td><?php echo htmlspecialchars($krs['nama_matkul']); ?></td>
                            <td><?php echo $krs['sks']; ?></td>
                            <td><?php echo htmlspecialchars($krs['nama_dosen'] ?? '-') . ' ' . htmlspecialchars($krs['gelar'] ?? ''); ?></td>
                            <td class="aksi">
                                <a href="?batal=<?php echo $krs['id_matkul']; ?>" class="btn-batal" onclick="return confirm('Yakin ingin membatalkan mata kuliah ini?')">Batal</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total-sks">Total SKS yang diambil: <?php echo $total_sks; ?> SKS</div>
        <?php else: ?>
            <div class="kosong">
                <p>Belum ada mata kuliah yang diambil.</p>
                <a href="isi_krs.php" class="btn-edit">Isi KRS</a>
            </div>
        <?php endif; ?>

        <div class="no-print">
            <a href="dashboard.php" class="btn-back">Kembali ke Dashboard</a>
            <a href="isi_krs.php" class="btn-edit">Edit KRS</a>
            <button onclick="window.print()" class="btn-edit" style="background-color:#f39c12;">Cetak KRS</button>
        </div>
    </div>
</body>

</html>
<?php mysqli_close($konek); ?>