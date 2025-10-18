<?php
// Detectar si estamos en local o en producción
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    // 🔹 Configuración LOCAL (XAMPP)
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "hotelandino";
} else {
    // 🔹 Configuración PRODUCCIÓN (hosting)
    $servername = "localhost";
    $username   = "hotelandino_user";
    $password   = "password"; // <-- pon aquí la real
    $dbname     = "hotelandino";
}

// Conexión
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Validar conexión
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Charset recomendado para acentos y emojis
mysqli_set_charset($conn, 'utf8mb4');

// Debug opcional (solo para desarrollo)
// echo "✅ Conexión establecida a la BD: $dbname";
?>