<?php
session_start();
include '../config.php';

if (!isset($_SESSION['adminmail'])) {
    header('Location: ../index.php');
    exit;
}

mysqli_query($conn, "ALTER TABLE room MODIFY status ENUM('Disponible','Reservada','Limpieza','Ocupada') NOT NULL DEFAULT 'Disponible'");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS room_stays (
    room_id INT NOT NULL PRIMARY KEY,
    guest_id VARCHAR(40) NOT NULL,
    guest_name VARCHAR(120) NOT NULL,
    nationality VARCHAR(80) NOT NULL,
    check_in_date DATE NOT NULL,
    check_in_time TIME NOT NULL,
    check_out_date DATE NOT NULL,
    receptionist_email VARCHAR(190) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_room_stays_room FOREIGN KEY (room_id) REFERENCES room(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$sql = "SELECT r.id, r.room_number, r.type, r.floor, r.status, rs.guest_name, rs.check_in_date, rs.check_out_date, rs.nationality
        FROM room r
        LEFT JOIN room_stays rs ON rs.room_id = r.id
        WHERE r.status IN ('Ocupada','Reservada','Limpieza')
        ORDER BY r.status, r.floor, r.room_number";
$result = mysqli_query($conn, $sql);

$roomsByStatus = [
    'Ocupada' => [],
    'Reservada' => [],
    'Limpieza' => [],
];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $roomsByStatus[$row['status']][] = $row;
    }
    mysqli_free_result($result);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hotel Andino - Estado de habitaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/room-status.css">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Estado de habitaciones</h2>
                <p class="text-muted mb-0">Consulta rápidamente qué habitaciones están ocupadas, reservadas o en limpieza.</p>
            </div>
            <span class="badge bg-secondary">Actualizado: <?php echo date('d/m/Y H:i'); ?></span>
        </div>

        <div class="row g-4">
            <?php
            $labels = [
                'Ocupada' => ['title' => 'Habitaciones ocupadas', 'class' => 'danger'],
                'Reservada' => ['title' => 'Habitaciones reservadas', 'class' => 'warning'],
                'Limpieza' => ['title' => 'En limpieza', 'class' => 'info'],
            ];
            foreach ($labels as $status => $meta):
                $items = $roomsByStatus[$status] ?? [];
            ?>
            <div class="col-lg-4">
                <div class="status-card border-<?php echo $meta['class']; ?>">
                    <div class="status-header bg-<?php echo $meta['class']; ?> text-white">
                        <h5 class="mb-0"><?php echo $meta['title']; ?></h5>
                        <span class="badge bg-light text-dark ms-2"><?php echo count($items); ?></span>
                    </div>
                    <div class="status-body">
                        <?php if (count($items) === 0): ?>
                            <p class="text-muted mb-0">No hay habitaciones en esta categoría.</p>
                        <?php else: ?>
                            <ul class="list-unstyled mb-0">
                                <?php foreach ($items as $room): ?>
                                    <li class="status-item">
                                        <div class="fw-bold">Hab. <?php echo htmlspecialchars($room['room_number']); ?> · Piso <?php echo (int)$room['floor']; ?></div>
                                        <div class="text-muted small">Tipo: <?php echo htmlspecialchars($room['type']); ?></div>
                                        <?php if (!empty($room['guest_name'])): ?>
                                            <div class="small">Huésped: <?php echo htmlspecialchars($room['guest_name']); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($room['nationality'])): ?>
                                            <div class="text-muted small">Nacionalidad: <?php echo htmlspecialchars($room['nationality']); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($room['check_in_date'])): ?>
                                            <div class="text-muted small">Ingreso: <?php echo date('d/m/Y', strtotime($room['check_in_date'])); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($room['check_out_date'])): ?>
                                            <div class="text-muted small">Salida: <?php echo date('d/m/Y', strtotime($room['check_out_date'])); ?></div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
