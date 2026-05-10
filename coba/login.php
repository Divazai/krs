<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Login Sistem KRS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .box {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ccc;
        }

        h2 {
            text-align: center;
            margin-top: 0;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;

        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #3498db;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input:focus {
            outline: none;
            border-color: #3498db;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #3498db;
            border: none;
            color: white;
            font-weight: bold;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 5px;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        .error {
            background: #f2dede;
            color: #a94442;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
            border: 1px solid #ebccd1;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>

<body>

    <div class="box">
        <h2>Login KRS</h2>

        <form action="proses_login.php" method="POST">
            <tr>
                <td><label> NIM </label></td>
                <td><input type="text" name="nim" placeholder="Masukkan NIM" required autofocus></td>
            </tr>
            <tr>
                <td><label> Password </label></td>
                <td><input type="password" name="password" placeholder="Masukkan Password" required></td>
            </tr>

            <?php if (isset($_GET['pesan'])): ?>
                <?php if ($_GET['pesan'] == 'gagal'): ?>
                    <div class="msg err"> NIM atau Password salah!</div>
                <?php elseif ($_GET['pesan'] == 'belum_login'): ?>
                    <div class="msg err"> Silakan login terlebih dahulu</div>
                <?php elseif ($_GET['pesan'] == 'logout'): ?>
                    <div class="msg ok"> Berhasil logout!</div>
                <?php endif; ?>
            <?php endif; ?>


            <button type="submit">LOGIN</button>
        </form>
    </div>

</body>

</html>