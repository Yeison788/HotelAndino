<?php
session_start();
include 'config.php';

// (opcional) proteger por login de usuario estándar
if (empty($_SESSION['usermail'])) { header("Location: index.php"); exit; }

/* Coordenadas del hotel (REEMPLAZA por las tuyas) */
$HOTEL_LAT = 5.9784153;     // <-- latitud
$HOTEL_LNG = -74.5935049;   // <-- longitud

/* Tu API KEY de Google (puedes sacarla de config.php si la guardas ahí) */
$GOOGLE_MAPS_API_KEY = 'AIzaSyCe3Gv31Uv-174yWTCuOI67tkFQojj7Q2E';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Atracciones cercanas - Hotel Andino</title>

  <!-- Bootstrap (opcional, ya lo usas) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Estilos de la página -->
  <link rel="stylesheet" href="./css/turismo.css?v=1">
</head>
<body>

<!-- Navbar muy simple para esta vista -->
<nav class="navbar navbar-light bg-white shadow-sm sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center gap-2" href="home.php">
      <img src="./image/LogoAndino.png" alt="logo" style="height:36px">
      <span>Hotel Andino</span>
    </a>
    <a class="btn btn-outline-dark" href="home.php">Volver</a>
  </div>
</nav>

<div class="container py-3">
  <!-- Filtros -->
  <div class="toolbar card shadow-sm p-3 mb-3">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-3">
        <label class="form-label mb-1">Radio</label>
        <select id="radius" class="form-select">
          <option value="500">500 m</option>
          <option value="1000" selected>1 km</option>
          <option value="2000">2 km</option>
          <option value="5000">5 km</option>
        </select>
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label mb-1">Categorías</label>
        <div class="d-flex flex-wrap gap-3" id="categories">
          <label class="form-check me-2"><input class="form-check-input" type="checkbox" value="tourist_attraction" checked> Atracciones</label>
          <label class="form-check me-2"><input class="form-check-input" type="checkbox" value="museum"> Museos</label>
          <label class="form-check me-2"><input class="form-check-input" type="checkbox" value="park"> Parques</label>
          <label class="form-check me-2"><input class="form-check-input" type="checkbox" value="church"> Iglesias</label>
          <label class="form-check me-2"><input class="form-check-input" type="checkbox" value="art_gallery"> Galerías</label>
        </div>
      </div>

      <div class="col-12 col-md-3 d-grid">
        <button id="btnBuscar" class="btn btn-dark">Buscar</button>
      </div>
    </div>
  </div>

  <!-- Mapa -->
  <div id="map"></div>
</div>

<script>
  // Pasamos coords del hotel al JS
  window.HOTEL = { lat: <?= json_encode(5.9784153) ?>, lng: <?= json_encode(-74.5935049) ?> };
</script>

<script src="./javascript/turismo.js?v=1"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCe3Gv31Uv-174yWTCuOI67tkFQojj7Q2E&libraries=places&callback=initMap&language=es"></script>
</body>
</html>
