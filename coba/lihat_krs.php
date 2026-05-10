<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php?pesan=belum_login");
    exit;
}

$nim = $_SESSION['nim'];

$query_mhs = "SELECT id_mahasiswa, nama, semester FROM mahasiswa WHERE nim = '$nim'";
$hasil_mhs = mysqli_query($konek, $query_mhs);
$data_mhs = mysqli_fetch_assoc($hasil_mhs);

if (!$data_mhs) {
    die("Data mahasiswa tidak ditemukan.");
}

$id_mahasiswa = $data_mhs['id_mahasiswa'];
$nama = $data_mhs['nama'];
$semester = $data_mhs['semester'];

if (isset($_GET['batal'])) {
    $id_matkul = $_GET['batal'];

    $cek_krs = "SELECT id_krs FROM krs WHERE id_mahasiswa = $id_mahasiswa AND id_matkul = $id_matkul AND semester_ambil = $semester";
    $hasil_cek = mysqli_query($konek, $cek_krs);

    if (mysqli_num_rows($hasil_cek) > 0) {
        $hapus = "DELETE FROM krs WHERE id_mahasiswa = $id_mahasiswa AND id_matkul = $id_matkul AND semester_ambil = $semester";
        $query_hapus = mysqli_query($konek, $hapus);

        if ($query_hapus) {
            $update_kapasitas = "UPDATE mata_kuliah SET terisi = terisi - 1 WHERE id_matkul = $id_matkul";
            mysqli_query($konek, $update_kapasitas);

            $pesan = "Mata kuliah berhasil dibatalkan.";
        } else {
            $pesan = "Gagal membatalkan mata kuliah.";
        }
    } else {
        $pesan = "Mata kuliah tidak ditemukan dalam KRS Anda.";
    }

    // Redirect agar tidak terjadi pengiriman ulang data
    header("Location: krs_saya.php?info=" . urlencode($pesan));
    exit;
}

$info = "";
if (isset($_GET['info'])) {
    $info = $_GET['info'];
}

$query_krs = "SELECT mk.kode_matkul, mk.nama_matkul, mk.sks, d.nama_dosen 
              FROM krs k
              JOIN mata_kuliah mk ON k.id_matkul = mk.id_matkul
              LEFT JOIN dosen d ON mk.id_dosen = d.id_dosen
              WHERE k.id_mahasiswa = $id_mahasiswa AND k.semester_ambil = $semester AND k.status = 'aktif'";

$hasil_krs = mysqli_query($konek, $query_krs);

$total_sks = 0;
$krs_array = array();

while ($row = mysqli_fetch_assoc($hasil_krs)) {
    $total_sks = $total_sks + $row['sks'];
    $krs_array[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>KRS Saya - <?php echo htmlspecialchars($nama); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }

        .info {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #bdc3c7;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        .btn-batal {
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
        }

        .btn-batal:hover {
            background-color: #c0392b;
        }

        .btn {
            display: inline-block;
            margin-top: 15px;
            background: #2c3e50;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
        }

        .total {
            font-weight: bold;
            margin-top: 15px;
            text-align: right;
        }

        .kosong {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
        }

        .pesan {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Kartu Rencana Studi (KRS)</h2>
        <div class="info">
            <strong>Nama:</strong> <?php echo htmlspecialchars($nama); ?><br>
            <strong>NIM:</strong> <?php echo htmlspecialchars($nim); ?><br>
            <strong>Semester:</strong> <?php echo $semester; ?>
        </div>

        <?php if ($info != "") { ?>
            <div class="pesan"><?php echo htmlspecialchars($info); ?></div>
        <?php } ?>

        <?php if (count($krs_array) > 0) { ?>
            <table>
                <tr>
                    <th>No</th>
                    <th>Kode MK</th>
                    <th>Nama Mata Kuliah</th>
                    <th>SKS</th>
                    <th>Dosen</th>
                    <th>Aksi</th>
                </tr>
                <?php
                $no = 1;
                foreach ($krs_array as $krs) {
                    echo "<tr>";
                    echo "<td>" . $no . "</td>";
                    echo "<td>" . htmlspecialchars($krs['kode_matkul']) . "</td>";
                    echo "<td>" . htmlspecialchars($krs['nama_matkul']) . "</td>";
                    echo "<td>" . $krs['sks'] . "</td>";
                    echo "<td>" . htmlspecialchars($krs['nama_dosen']) . "</td>";
                    echo "<td><a href='krs_saya.php?batal=" . $krs['id_matkul'] . "' class='btn-batal' onclick='return confirm(\"Yakin batal?\")'>Batal</a></td>";
                    echo "</tr>";
                    $no++;
                }
                ?>
            </table>
            <div class="total">Total SKS: <?php echo $total_sks; ?></div>
        <?php } else { ?>
            <div class="kosong">
                <p>Belum ada mata kuliah yang diambil.</p>
                <a href="isi_krs.php" class="btn">Isi KRS</a>
            </div>
        <?php } ?>

        <a href="dashboard.php" class="btn">Kembali ke Dashboard</a>
        <a href="isi_krs.php" class="btn" style="background:#27ae60;">Tambah/Edit KRS</a>
        <button onclick="window.print()" class="btn" style="background:#f39c12;">Cetak KRS</button>
    </div>
</body>

</html>

<?php
mysqli_close($konek);
?>