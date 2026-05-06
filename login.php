<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Sistem KRS</title>
<style>
body{font-family:'Segoe UI';background:#f5f5f5}
.box{
    width:360px;margin:100px auto;background:#fff;padding:28px;
    border-radius:10px;box-shadow:0 6px 16px rgba(0,0,0,.1);
    border-top:5px solid #4CAF50;
}
h2{margin-bottom:15px;text-align:center}
input{
    width:100%;padding:10px;margin:10px 0;
    border:1px solid #ccc;border-radius:6px;
}
button{
    width:100%;padding:10px;background:#4CAF50;border:none;
    color:#fff;font-weight:600;border-radius:6px;cursor:pointer;
}
.msg{padding:10px;border-radius:6px;margin-bottom:10px;text-align:center}
.err{background:#ffe3e3;color:#b00020}
.ok{background:#e6ffed;color:#1b5e20}
</style>
</head>
<body>

<div class="box">
  <h2>Login Sistem KRS</h2>

  <?php if(isset($_GET['pesan'])): ?>
    <?php if($_GET['pesan']=='gagal'): ?>
      <div class="msg err">❌ NIM atau Password salah!</div>
    <?php elseif($_GET['pesan']=='belum_login'): ?>
      <div class="msg err">⚠️ Silakan login terlebih dahulu</div>
    <?php elseif($_GET['pesan']=='logout'): ?>
      <div class="msg ok">✅ Berhasil logout</div>
    <?php endif; ?>
  <?php endif; ?>

  <form action="proses_login.php" method="POST">
    <input type="text" name="nim" placeholder="Masukkan NIM" required autofocus>
    <input type="password" name="password" placeholder="Masukkan Password" required>
    <button type="submit">LOGIN</button>
  </form>
</div>

</body>
</html>