<?php
session_start();
include 'coba/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nim = $_POST['nim'];
    $password = $_POST['password'];

    $query = "SELECT * FROM mahasiswa WHERE nim='$nim' AND password='$password'";
    $result = mysqli_query($konek, $query);

    if (mysqli_num_rows($result) == 1) {
        $data = mysqli_fetch_assoc($result);

        $_SESSION['login'] = true;
        $_SESSION['id_mahasiswa'] = $data['id_mahasiswa'];
        $_SESSION['nim'] = $data['nim'];
        $_SESSION['nama'] = $data['nama'];

        header("Location: dashboard.php");
        exit;
    } else {
        header("Location: login.php?pesan=gagal");
        exit;
    }
} else {
    echo "Akses tidak sah!";
}
