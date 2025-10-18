<?php
session_start();
include '../config.php';
require_once __DIR__ . '/includes/admin_bootstrap.php';

ensureEmpStructure($conn);
ensureRoomRates($conn);
admin_refresh_session($conn, $_SESSION['adminmail'] ?? '');
admin_require_permission('personal');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Andino - Admin | Personal</title>

    <!-- fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" 
          integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" 
          crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" 
            crossorigin="anonymous"></script>

    <!-- estilos (puedes crear staff.css después si quieres aislar) -->
    <link rel="stylesheet" href="css/staff.css">

    <style>
        /* Estilos específicos de staff para que no choquen con room */
        .staff-page .roombox {
            background-color: #eef2ff;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0,0,0,.12);
            transition: transform .2s ease;
        }
        .staff-page .roombox:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,.18);
        }
        .staff-page .roombox h3 {
            font-size: 20px;
            font-weight: 600;
            margin-top: 10px;
        }
    </style>
</head>

<body>
<div class="staff-page"><!-- 👈 Nuevo wrapper -->

    <!-- Formulario agregar personal -->
    <div class="addroomsection">
        <form action="" method="POST">
            <label for="troom">Nombre :</label>
            <input type="text" name="staffname" class="form-control">

            <label for="bed">Cargo :</label>
            <select name="staffwork" class="form-control">
                <option value selected></option>
                <option value="Manager">Gerente</option>
                <option value="Cook">Cocinero</option>
                <option value="Helper">Ayudante</option>
                <option value="cleaner">Limpieza</option>
                <option value="weighter">Mesero</option>
            </select>

            <button type="submit" class="btn btn-success" name="addstaff">Agregar personal</button>
        </form>

        <?php
        if (isset($_POST['addstaff'])) {
            $staffname = $_POST['staffname'];
            $staffwork = $_POST['staffwork'];

            $sql = "INSERT INTO staff(name,work) VALUES ('$staffname', '$staffwork')";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                header("Location: staff.php");
            }
        }
        ?>
    </div>

    <!-- Lista de personal -->
    <div class="room d-flex flex-wrap justify-content-start mt-5">
    <?php
        $sql = "SELECT * FROM staff";
        $re = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_array($re)) {
            echo "
            <div class='roombox text-center m-2'>
                <i class='fa fa-users fa-3x mb-2'></i>
                <h3>{$row['name']}</h3>
                <div class='mb-2'>{$row['work']}</div>
                <a href='staffdelete.php?id={$row['id']}'>
                    <button class='btn btn-danger btn-sm'>Eliminar</button>
                </a>
            </div>";
        }
    ?>
    </div>

</div><!-- /staff-page -->
</body>
</html>
