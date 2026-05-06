<?php
session_start();

// Proteksi halaman
if (!isset($_SESSION['login'])) {
    header("Location: login.php?pesan=belum_login");
    exit;
}
?>
<title>Dashboard KRS</title>
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
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            max-width: 500px;
            background: #ffffff;
            padding: 50px 40px 40px 40px;
            position: relative;
            box-shadow: 5px 5px 15px rgba(0,0,0,0.1);
            transform: rotate(1deg);
            border: 1px solid #ddd;
        }
        /* Push Pin */
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
        .sticky-note {
            background: #faedcd;
            padding: 25px;
            transform: rotate(-2deg);
            box-shadow: 3px 3px 10px rgba(0,0,0,0.1);
            border-left: 5px solid #dda15e;
            margin: 20px 0;
            position: relative;
        }
        .sticky-note::before {
            content: "";
            width: 16px;
            height: 16px;
            background: #2a9d8f;
            border-radius: 50%;
            position: absolute;
            top: 5px;
            right: 5px;
            box-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }
        .user-info p {
            margin: 5px 0;
            font-size: 20px;
            color: #283618;
            font-weight: 600;
        }
        .nim-badge {
            display: inline-block;
            background: #dda15e;
            color: #fff;
            padding: 2px 12px;
            border-radius: 20px;
            font-weight: 800;
            margin-top: 5px;
            font-size: 16px;
        }
        .btn-logout {
            display: block;
            text-decoration: none;
            padding: 12px;
            background: #e63946;
            color: #fff;
            font-weight: 800;
            font-size: 18px;
            border-radius: 50px;
            text-align: center;
            box-shadow: 3px 3px 0px #9b2226;
            transition: all 0.2s;
            margin-top: 30px;
            text-transform: uppercase;
        }
        .btn-logout:hover {
            background: #f0505c;
            transform: translateY(-2px);
            box-shadow: 5px 5px 0px #9b2226;
        }
        .btn-logout:active {
            transform: translateY(2px);
            box-shadow: 1px 1px 0px #9b2226;
        }

        @media (max-width: 480px) {
            .container { padding: 40px 25px 30px 25px; transform: rotate(0deg); }
            .sticky-note { transform: rotate(0deg); }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="push-pin"></div>
    <h2>Halo, Mahasiswa! 🎒</h2>
    
    <div class="sticky-note">
        <div class="user-info">
            <p>Nama: <?= $_SESSION['nama']; ?></p>
            <div class="nim-badge">NIM: <?= $_SESSION['nim']; ?></div>
        </div>
    </div>

    <a href="logout.php" class="btn-logout">KELUAR (LOGOUT)</a>
</div>

</body>
</html>