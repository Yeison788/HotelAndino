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
            <li class="pagebtn active"><img src="../image/icon/dashboard.png">&nbsp;&nbsp; Panel</li>
            <li class="pagebtn"><img src="../image/icon/bed.png">&nbsp;&nbsp; Reservas</li>
            <li class="pagebtn"><img src="../image/icon/wallet.png">&nbsp;&nbsp; Pagos</li>            
            <li class="pagebtn"><img src="../image/icon/bedroom.png">&nbsp;&nbsp; Habitaciones</li>
            <li class="pagebtn"><img src="../image/icon/staff.png">&nbsp;&nbsp; Personal</li>
            <li class="pagebtn"><img src="../image/icon/analytics.png">&nbsp;&nbsp; Analíticas</li>
            <li class="pagebtn"><img src="../image/icon/security1.png">&nbsp;&nbsp; Seguridad</li>
        </ul>
    </nav>

    <!-- main section -->
    <div class="mainscreen">
        <iframe class="frames frame1 active" src="./dashboard.php" frameborder="0"></iframe>
        <iframe class="frames frame2" src="./roombook.php" frameborder="0"></iframe>
        <iframe class="frames frame3" src="./payment.php" frameborder="0"></iframe>
        <iframe class="frames frame4" src="./room.php" frameborder="0"></iframe>
        <iframe class="frames frame5" src="./staff.php" frameborder="0"></iframe>
        <iframe class="frames frame6" src="./analytics.php" frameborder="0"></iframe>
        <iframe class="frames frame7" src="./security.php" frameborder="0"></iframe>
    </div>

    <script src="./javascript/script.js"></script>
</body>
</html>
