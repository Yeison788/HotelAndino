<?php
include '../config.php';
session_start();

// Verificación de sesión admin
$adminmail = $_SESSION['adminmail'] ?? '';
if (!$adminmail) {
  header("location: ../index.php");
  exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Andino - Admin</title>

    <!-- admin.css (ya con paleta dorada) -->
    <link rel="stylesheet" href="./css/admin.css">

    <!-- loading bar -->
    <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>

    <!-- fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" 
          integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" 
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
</head>

<body>
    <!-- mobile view -->
    <div id="mobileview">
        <h5>El panel de administración no está disponible en dispositivos móviles</h5>
    </div>
  
    <!-- nav bar -->
    <nav class="uppernav">
        <div class="logo">
            <img class="HotelAndino" src="../image/LogoAndino.png" alt="logo">
            <p>Hotel Andino</p>
        </div>
        <div class="logout">
            <a href="../logout.php"><button class="btn btn-primary">Cerrar sesión</button></a>
        </div>
    </nav>

    <!-- side nav -->
    <nav class="sidenav">
        <ul>
            <li class="pagebtn active" data-frame="0" data-src="./dashboard.php"><i class="fa-solid fa-chart-line"></i>&nbsp;&nbsp; Panel</li>
            <li class="pagebtn" data-frame="1" data-src="./roombook.php"><i class="fa-solid fa-bed"></i>&nbsp;&nbsp; Reservas</li>
            <li class="pagebtn" data-frame="2" data-src="./payment.php"><i class="fa-solid fa-money-bill-wave"></i>&nbsp;&nbsp; Pagos</li>
            <li class="pagebtn" data-frame="3" data-src="./room.php"><i class="fa-solid fa-house"></i>&nbsp;&nbsp; Habitaciones</li>
            <li class="pagebtn" data-frame="4" data-src="./staff.php"><i class="fa-solid fa-user-group"></i>&nbsp;&nbsp; Personal</li>
            <li class="has-submenu">
                <div class="submenu-trigger" tabindex="0"><i class="fa-solid fa-clipboard-list"></i>&nbsp;&nbsp; Registros</div>
                <ul class="submenu">
                    <li class="pagebtn" data-frame="5" data-src="./records.php?view=rooms"><i class="fa-solid fa-door-open"></i>&nbsp;&nbsp; Registrar habitación</li>
                    <li class="pagebtn" data-frame="5" data-src="./records.php?view=room-types"><i class="fa-solid fa-layer-group"></i>&nbsp;&nbsp; Registrar tipo de habitación</li>
                    <li class="pagebtn" data-frame="5" data-src="./records.php?view=users"><i class="fa-solid fa-user-plus"></i>&nbsp;&nbsp; Registrar usuario</li>
                    <li class="pagebtn" data-frame="5" data-src="./records.php?view=products"><i class="fa-solid fa-box"></i>&nbsp;&nbsp; Registrar producto</li>
                    <li class="pagebtn" data-frame="5" data-src="./records.php?view=sales"><i class="fa-solid fa-cash-register"></i>&nbsp;&nbsp; Registrar venta</li>
                    <li class="pagebtn" data-frame="5" data-src="./records.php?view=summary"><i class="fa-solid fa-chart-line"></i>&nbsp;&nbsp; Resumen rápido</li>
                </ul>
            </li>
            <li class="pagebtn" data-frame="6" data-src="./room-status.php"><i class="fa-solid fa-eye"></i>&nbsp;&nbsp; Ver</li>
        </ul>
    </nav>

    <!-- main section -->
    <div class="mainscreen">
        <iframe class="frames frame1 active" src="./dashboard.php" frameborder="0"></iframe>
        <iframe class="frames frame2" src="./roombook.php" frameborder="0"></iframe>
        <iframe class="frames frame3" src="./payment.php" frameborder="0"></iframe>
        <iframe class="frames frame4" src="./room.php" frameborder="0"></iframe>
        <iframe class="frames frame5" src="./staff.php" frameborder="0"></iframe>
        <iframe class="frames frame6" src="./records.php?view=summary" frameborder="0"></iframe>
        <iframe class="frames frame7" src="./room-status.php" frameborder="0"></iframe>
    </div>

    <script src="./javascript/script.js"></script>
</body>
</html>
