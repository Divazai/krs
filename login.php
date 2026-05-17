<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Sistem KRS</title>
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Baloo 2', cursive;
        background-color: #fefae0;
        background-image: 
            linear-gradient(#e9edc9 1px, transparent 1px),
            linear-gradient(90deg, #e9edc9 1px, transparent 1px);
        background-size: 20px 20px;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 20px;
    }
    .box {
        width: 100%;
        max-width: 380px;
        background: #ffffff;
        padding: 50px 40px 40px 40px;
        position: relative;
        box-shadow: 5px 5px 15px rgba(0,0,0,0.1);
        transform: rotate(-1.5deg);
        border: 1px solid #ddd;
    }
    .box::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 100%;
        height: 10px;
        background: radial-gradient(circle, transparent, transparent 50%, #fff 50%, #fff) 0% 0% / 20px 20px;
        transform: rotate(180deg);
    }
    .push-pin {
        width: 24px;
        height: 24px;
        background: #e63946;
        border-radius: 50%;
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        box-shadow: 2px 2px 5px rgba(0,0,0,0.3);
        z-index: 10;
    }
    .push-pin::after {
        content: "";
        width: 8px;
        height: 8px;
        background: rgba(255,255,255,0.4);
        border-radius: 50%;
        position: absolute;
        top: 4px;
        left: 4px;
    }
    h2 {
        color: #606c38;
        font-weight: 800;
        text-align: center;
        margin-top: 0;
        margin-bottom: 25px;
        border-bottom: 2px dashed #ccd5ae;
        padding-bottom: 10px;
    }
    input {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 15px;
        border: 2px solid #ccd5ae;
        border-radius: 12px;
        font-family: inherit;
        font-size: 16px;
        background: #fefdf0;
        box-sizing: border-box;
        transition: all 0.2s;
    }
    input:focus {
        outline: none;
        border-color: #606c38;
        background: #fff;
        transform: scale(1.02);
    }
    button {
        width: 100%;
        padding: 15px;
        background: #bc6c25;
        border: none;
        color: #fff;
        font-weight: 800;
        font-size: 18px;
        border-radius: 50px;
        cursor: pointer;
        box-shadow: 3px 3px 0px #8b4513;
        transition: all 0.2s;
        margin-top: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    button:hover {
        background: #dda15e;
        transform: translateY(-2px);
        box-shadow: 5px 5px 0px #8b4513;
    }
    button:active {
        transform: translateY(2px);
        box-shadow: 1px 1px 0px #8b4513;
    }
    .msg {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 15px;
        text-align: center;
        font-weight: 600;
        font-size: 14px;
        border: 2px dotted;
    }
    .err { background: #ffe3e3; color: #b00020; border-color: #b00020; }
    .ok { background: #e6ffed; color: #1b5e20; border-color: #1b5e20; }

    @media (max-width: 400px) {
        .box { padding: 40px 25px 30px 25px; transform: rotate(0deg); }
    }
</style>
</head>
<body>

<div class="box">
  <div class="push-pin"></div>
  <h2>KRS</h2>

  <?php if(isset($_GET['pesan'])): ?>
    <?php if($_GET['pesan']=='gagal'): ?>
      <div class="msg err"> NIM atau Password salah!</div>
    <?php elseif($_GET['pesan']=='belum_login'): ?>
      <div class="msg err"> Silakan login terlebih dahulu</div>
    <?php elseif($_GET['pesan']=='logout'): ?>
      <div class="msg ok"> Berhasil logout</div>
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