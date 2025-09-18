<?php
session_start();
include '../config.php';

// ---- Cambiar estado (AJAX) ----
if (isset($_POST['change_status'])) {
    $roomId = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
    $newStatus = $_POST['status'] ?? '';
    $allowed = ['Disponible','Pendiente','Ocupada'];

    header('Content-Type: application/json; charset=utf-8');

    if ($roomId <= 0 || !in_array($newStatus, $allowed, true)) {
        http_response_code(400);
        echo json_encode(['ok'=>false, 'error'=>'Parámetros inválidos']);
        exit;
    }

    if ($stmt = $conn->prepare("UPDATE room SET status=? WHERE id=?")) {
        $stmt->bind_param('si', $newStatus, $roomId);
        $ok = $stmt->execute();
        $stmt->close();
        echo json_encode([
            'ok' => (bool)$ok,
            'room_id' => $roomId,
            'status' => $newStatus
        ]);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['ok'=>false, 'error'=>'Error en la consulta']);
        exit;
    }
}

// ---- Editar tipo/cama ----
if (isset($_POST['edit_room'])) {
    $roomId = intval($_POST['room_id'] ?? 0);
    $type = $_POST['type'] ?? '';
    $bedding = $_POST['bedding'] ?? '';
    $sql = "UPDATE room SET type='$type', bedding='$bedding' WHERE id='$roomId'";
    mysqli_query($conn, $sql);
    header("Location: room.php?floor=" . ($_GET['floor'] ?? ''));
    exit;
}

// ---- Filtro de piso ----
$currentFloor = isset($_GET['floor']) ? intval($_GET['floor']) : 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Hotel Andino - Habitaciones</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/room.css">
</head>
<body class="p-4">
  <div class="room-page">
    <div class="container">
      <h2 class="mb-4">Gestión de Habitaciones</h2>

      <!-- Selector de piso -->
      <form method="get" class="mb-4">
        <label for="floor" class="form-label">Seleccionar piso:</label>
        <select name="floor" id="floor" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
          <option value="1" <?= $currentFloor==1?'selected':'' ?>>Piso 1</option>
          <option value="2" <?= $currentFloor==2?'selected':'' ?>>Piso 2</option>
          <option value="3" <?= $currentFloor==3?'selected':'' ?>>Piso 3</option>
          <option value="4" <?= $currentFloor==4?'selected':'' ?>>Piso 4</option>
        </select>
      </form>

      <div class="row g-3">
        <?php
        $sql = "SELECT * FROM room WHERE floor='$currentFloor' ORDER BY room_number";
        $re = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($re)) {
          $statusClass = match ($row['status']) {
            'Disponible' => 'bg-success text-white',
            'Pendiente' => 'bg-warning text-dark',
            'Ocupada' => 'bg-danger text-white',
            default => 'bg-secondary text-white'
          };

          echo "
          <div class='col-md-3'>
            <div class='card shadow-sm'>
              <div class='card-body text-center' id='room-{$row['id']}'>
                
                <!-- Icono con badge encima -->
                <div class='icon-wrapper mb-2'>
                  <i class='fa-solid fa-bed fa-2x'></i>
                  <span class='badge $statusClass'>{$row['status']}</span>
                </div>

                <h5 class='card-title mb-1'>Hab. {$row['room_number']}</h5>
                <p class='mb-1'>{$row['type']} - {$row['bedding']}</p>
                
                <!-- Form estado (AJAX) -->
                <form method='POST' class='d-flex justify-content-center gap-1 mb-2 js-status-form' data-room='{$row['id']}'>
                  <input type='hidden' name='room_id' value='{$row['id']}'>
                  <input type='hidden' name='change_status' value='1'>
                  <button type='submit' name='status' value='Disponible' class='btn btn-sm btn-success'>Disponible</button>
                  <button type='submit' name='status' value='Pendiente'  class='btn btn-sm btn-warning'>Pendiente</button>
                  <button type='submit' name='status' value='Ocupada'    class='btn btn-sm btn-danger'>Ocupada</button>
                </form>

                <!-- Form edición (con recarga) -->
                <form method='POST' class='mt-2'>
                  <input type='hidden' name='room_id' value='{$row['id']}'>
                  <input type='hidden' name='edit_room' value='1'>
                  <select name='type' class='form-select form-select-sm mb-1'>
                    <option " . ($row['type']=='Superior Room'?'selected':'') . ">Superior Room</option>
                    <option " . ($row['type']=='Deluxe Room'?'selected':'') . ">Deluxe Room</option>
                    <option " . ($row['type']=='Guest House'?'selected':'') . ">Guest House</option>
                    <option " . ($row['type']=='Single Room'?'selected':'') . ">Single Room</option>
                  </select>
                  <select name='bedding' class='form-select form-select-sm mb-1'>
                    <option " . ($row['bedding']=='Single'?'selected':'') . ">Single</option>
                    <option " . ($row['bedding']=='Double'?'selected':'') . ">Double</option>
                    <option " . ($row['bedding']=='Triple'?'selected':'') . ">Triple</option>
                    <option " . ($row['bedding']=='Quad'?'selected':'') . ">Quad</option>
                  </select>
                  <button type='submit' class='btn btn-primary btn-sm w-100'>Guardar cambios</button>
                </form>
              </div>
            </div>
          </div>";
        }
        ?>
      </div>
    </div>
  </div>

  <!-- JS para AJAX -->
  <script src="javascript/room.js" defer></script>
</body>
</html>
