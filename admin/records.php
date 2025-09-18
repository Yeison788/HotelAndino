<?php
session_start();
include '../config.php';

if (!isset($_SESSION['adminmail'])) {
    header('Location: ../index.php');
    exit;
}

$adminEmail = $_SESSION['adminmail'];

// Asegurar estructuras necesarias
mysqli_query($conn, "ALTER TABLE room MODIFY status ENUM('Disponible','Reservada','Limpieza','Ocupada') NOT NULL DEFAULT 'Disponible'");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS room_types (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS products (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS sales (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    product_id INT NULL,
    details VARCHAR(190) NULL,
    quantity INT NOT NULL DEFAULT 1,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    sold_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sales_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_room') {
        $floor = intval($_POST['floor'] ?? 1);
        $number = trim($_POST['room_number'] ?? '');
        $type = trim($_POST['room_type'] ?? '');
        $bedding = isset($_POST['bedding']) ? (int)$_POST['bedding'] : 0;
        $status = trim($_POST['status'] ?? 'Disponible');

        if ($number === '' || $type === '' || $bedding < 1) {
            $messages[] = ['type' => 'danger', 'text' => 'Indica número, tipo y capacidad de la habitación.'];
        } else {
            $beddingValue = (string)$bedding;
            $stmt = $conn->prepare('INSERT INTO room (floor, room_number, type, bedding, status) VALUES (?, ?, ?, ?, ?)');
            if ($stmt) {
                $stmt->bind_param('issss', $floor, $number, $type, $beddingValue, $status);
                if ($stmt->execute()) {
                    $messages[] = ['type' => 'success', 'text' => 'Habitación registrada correctamente.'];
                } else {
                    $messages[] = ['type' => 'danger', 'text' => 'No se pudo registrar la habitación: ' . htmlspecialchars($stmt->error)];
                }
                $stmt->close();
            }
        }
    }

    if ($action === 'create_room_type') {
        $name = trim($_POST['type_name'] ?? '');
        $description = trim($_POST['type_description'] ?? '');

        if ($name === '') {
            $messages[] = ['type' => 'danger', 'text' => 'El nombre del tipo de habitación es obligatorio.'];
        } else {
            $stmt = $conn->prepare('INSERT INTO room_types (name, description) VALUES (?, ?)');
            if ($stmt) {
                $stmt->bind_param('ss', $name, $description);
                if ($stmt->execute()) {
                    $messages[] = ['type' => 'success', 'text' => 'Tipo de habitación agregado.'];
                } else {
                    $messages[] = ['type' => 'danger', 'text' => 'No se pudo agregar el tipo de habitación: ' . htmlspecialchars($stmt->error)];
                }
                $stmt->close();
            }
        }
    }

    if ($action === 'create_user') {
        $username = trim($_POST['user_name'] ?? '');
        $email = trim($_POST['user_email'] ?? '');
        $password = $_POST['user_password'] ?? '';

        if ($username === '' || $email === '' || $password === '') {
            $messages[] = ['type' => 'danger', 'text' => 'Todos los campos del usuario son obligatorios.'];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $messages[] = ['type' => 'danger', 'text' => 'El correo electrónico no es válido.'];
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare('INSERT INTO signup (Username, Email, Password) VALUES (?, ?, ?)');
            if ($stmt) {
                $stmt->bind_param('sss', $username, $email, $hash);
                if ($stmt->execute()) {
                    $messages[] = ['type' => 'success', 'text' => 'Usuario registrado correctamente.'];
                } else {
                    $messages[] = ['type' => 'danger', 'text' => 'No se pudo registrar el usuario: ' . htmlspecialchars($stmt->error)];
                }
                $stmt->close();
            }
        }
    }

    if ($action === 'create_product') {
        $name = trim($_POST['product_name'] ?? '');
        $price = floatval($_POST['product_price'] ?? 0);

        if ($name === '' || $price <= 0) {
            $messages[] = ['type' => 'danger', 'text' => 'Indica un nombre y un precio válido para el producto.'];
        } else {
            $stmt = $conn->prepare('INSERT INTO products (name, price) VALUES (?, ?)');
            if ($stmt) {
                $stmt->bind_param('sd', $name, $price);
                if ($stmt->execute()) {
                    $messages[] = ['type' => 'success', 'text' => 'Producto registrado correctamente.'];
                } else {
                    $messages[] = ['type' => 'danger', 'text' => 'No se pudo registrar el producto: ' . htmlspecialchars($stmt->error)];
                }
                $stmt->close();
            }
        }
    }

    if ($action === 'create_sale') {
        $productId = isset($_POST['sale_product']) && $_POST['sale_product'] !== '' ? intval($_POST['sale_product']) : null;
        $details = trim($_POST['sale_details'] ?? '');
        $quantity = max(1, intval($_POST['sale_quantity'] ?? 1));
        $total = floatval($_POST['sale_total'] ?? 0);

        if ($total <= 0) {
            $messages[] = ['type' => 'danger', 'text' => 'El total de la venta debe ser mayor que cero.'];
        } else {
            if ($productId === 0) {
                $productId = null;
            }

            if ($productId === null) {
                $stmt = $conn->prepare('INSERT INTO sales (product_id, details, quantity, total) VALUES (NULL, ?, ?, ?)');
                if ($stmt) {
                    $stmt->bind_param('sid', $details, $quantity, $total);
                }
            } else {
                $stmt = $conn->prepare('INSERT INTO sales (product_id, details, quantity, total) VALUES (?, ?, ?, ?)');
                if ($stmt) {
                    $stmt->bind_param('isid', $productId, $details, $quantity, $total);
                }
            }

            if (isset($stmt) && $stmt) {
                if ($stmt->execute()) {
                    $messages[] = ['type' => 'success', 'text' => 'Venta registrada correctamente.'];
                } else {
                    $messages[] = ['type' => 'danger', 'text' => 'No se pudo registrar la venta: ' . htmlspecialchars($stmt->error)];
                }
                $stmt->close();
            }
        }
    }
}

// Datos para selects y listados
$roomTypes = [];
if ($result = mysqli_query($conn, 'SELECT id, name FROM room_types ORDER BY name')) {
    while ($row = mysqli_fetch_assoc($result)) {
        $roomTypes[] = $row;
    }
    mysqli_free_result($result);
}

$products = [];
if ($result = mysqli_query($conn, 'SELECT id, name, price FROM products ORDER BY name')) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    mysqli_free_result($result);
}

$recentRooms = mysqli_query($conn, 'SELECT room_number, type, status, floor FROM room ORDER BY id DESC LIMIT 8');
$recentTypes = mysqli_query($conn, 'SELECT name, created_at FROM room_types ORDER BY created_at DESC LIMIT 8');
$recentUsers = mysqli_query($conn, 'SELECT Username, Email, CreatedAt FROM signup ORDER BY CreatedAt DESC LIMIT 8');
$recentSales = mysqli_query($conn, 'SELECT s.id, s.details, s.quantity, s.total, s.sold_at, p.name AS product_name FROM sales s LEFT JOIN products p ON p.id = s.product_id ORDER BY s.sold_at DESC LIMIT 10');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hotel Andino - Registros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/records.css">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Registros administrativos</h2>
                <p class="text-muted mb-0">Gestiona habitaciones, tipos, usuarios, productos y ventas desde un solo lugar.</p>
            </div>
            <span class="badge bg-dark">Sesión: <?php echo htmlspecialchars($adminEmail); ?></span>
        </div>

        <?php foreach ($messages as $message): ?>
            <div class="alert alert-<?php echo $message['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $message['text']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Registrar habitación</h5>
                        <form method="post" class="row g-3">
                            <input type="hidden" name="action" value="create_room">
                            <div class="col-md-4">
                                <label for="room_floor" class="form-label">Piso</label>
                                <select id="room_floor" name="floor" class="form-select">
                                    <?php for ($i = 1; $i <= 6; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="room_number" class="form-label">Número</label>
                                <input type="text" name="room_number" id="room_number" class="form-control" placeholder="301" required>
                            </div>
                            <div class="col-md-4">
                                <label for="room_status" class="form-label">Estado</label>
                                <select name="status" id="room_status" class="form-select">
                                    <option value="Disponible">Disponible</option>
                                    <option value="Reservada">Reservada</option>
                                    <option value="Limpieza">Limpieza</option>
                                    <option value="Ocupada">Ocupada</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="room_type" class="form-label">Tipo de habitación</label>
                                <input list="room_type_list" name="room_type" id="room_type" class="form-control" placeholder="Habitación Sencilla" required>
                                <datalist id="room_type_list">
                                    <?php foreach ($roomTypes as $type): ?>
                                        <option value="<?php echo htmlspecialchars($type['name']); ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="col-md-6">
                                <label for="room_bedding" class="form-label">Cantidad de huéspedes (capacidad)</label>
                                <select name="bedding" id="room_bedding" class="form-select" required>
                                    <option value="" disabled selected>Selecciona capacidad</option>
                                    <?php for ($guests = 1; $guests <= 6; $guests++): ?>
                                        <option value="<?php echo $guests; ?>"><?php echo $guests; ?> huésped<?php echo $guests > 1 ? 'es' : ''; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Guardar habitación</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Registrar tipo de habitación</h5>
                        <form method="post" class="row g-3">
                            <input type="hidden" name="action" value="create_room_type">
                            <div class="col-12">
                                <label for="type_name" class="form-label">Nombre</label>
                                <input type="text" name="type_name" id="type_name" class="form-control" placeholder="Suite Presidencial" required>
                            </div>
                            <div class="col-12">
                                <label for="type_description" class="form-label">Descripción</label>
                                <textarea name="type_description" id="type_description" rows="3" class="form-control" placeholder="Incluye balcón privado, sala y vista panorámica"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-outline-primary">Guardar tipo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Registrar usuario</h5>
                        <form method="post" class="row g-3">
                            <input type="hidden" name="action" value="create_user">
                            <div class="col-md-6">
                                <label for="user_name" class="form-label">Nombre</label>
                                <input type="text" id="user_name" name="user_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="user_email" class="form-label">Correo</label>
                                <input type="email" id="user_email" name="user_email" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label for="user_password" class="form-label">Contraseña</label>
                                <input type="password" id="user_password" name="user_password" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Registrar usuario</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Registrar producto</h5>
                        <form method="post" class="row g-3">
                            <input type="hidden" name="action" value="create_product">
                            <div class="col-md-8">
                                <label for="product_name" class="form-label">Nombre</label>
                                <input type="text" id="product_name" name="product_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="product_price" class="form-label">Precio</label>
                                <input type="number" id="product_price" name="product_price" min="0" step="0.01" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-outline-primary">Guardar producto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Registrar venta</h5>
                        <form method="post" class="row g-3">
                            <input type="hidden" name="action" value="create_sale">
                            <div class="col-md-6">
                                <label for="sale_product" class="form-label">Producto</label>
                                <select id="sale_product" name="sale_product" class="form-select">
                                    <option value="">-- Selecciona --</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>">
                                            <?php echo htmlspecialchars($product['name']); ?> (<?php echo number_format($product['price'], 0, ',', '.'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="sale_quantity" class="form-label">Cantidad</label>
                                <input type="number" id="sale_quantity" name="sale_quantity" min="1" class="form-control" value="1">
                            </div>
                            <div class="col-12">
                                <label for="sale_details" class="form-label">Descripción / Cliente</label>
                                <input type="text" id="sale_details" name="sale_details" class="form-control" placeholder="Ej. Minibar Hab. 204">
                            </div>
                            <div class="col-12">
                                <label for="sale_total" class="form-label">Total (COP)</label>
                                <input type="number" id="sale_total" name="sale_total" step="0.01" min="0" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">Guardar venta</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Resumen rápido</h5>
                        <div class="mb-3">
                            <h6 class="text-muted">Últimas habitaciones</h6>
                            <ul class="list-group list-group-flush">
                                <?php if ($recentRooms && mysqli_num_rows($recentRooms) > 0): ?>
                                    <?php while ($room = mysqli_fetch_assoc($recentRooms)): ?>
                                        <li class="list-group-item">
                                            Hab. <?php echo htmlspecialchars($room['room_number']); ?> · <?php echo htmlspecialchars($room['type']); ?> · Piso <?php echo (int)$room['floor']; ?> · <?php echo htmlspecialchars($room['status']); ?>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-muted">Sin registros recientes.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-muted">Tipos registrados</h6>
                            <ul class="list-group list-group-flush">
                                <?php if ($recentTypes && mysqli_num_rows($recentTypes) > 0): ?>
                                    <?php while ($type = mysqli_fetch_assoc($recentTypes)): ?>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span><?php echo htmlspecialchars($type['name']); ?></span>
                                            <small class="text-muted"><?php echo date('d/m/Y', strtotime($type['created_at'])); ?></small>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-muted">Añade tu primer tipo.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-muted">Usuarios recientes</h6>
                            <ul class="list-group list-group-flush">
                                <?php if ($recentUsers && mysqli_num_rows($recentUsers) > 0): ?>
                                    <?php while ($user = mysqli_fetch_assoc($recentUsers)): ?>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span><?php echo htmlspecialchars($user['Username']); ?></span>
                                            <small class="text-muted"><?php echo htmlspecialchars($user['Email']); ?></small>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-muted">Aún no hay usuarios nuevos.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div>
                            <h6 class="text-muted">Últimas ventas</h6>
                            <ul class="list-group list-group-flush">
                                <?php if ($recentSales && mysqli_num_rows($recentSales) > 0): ?>
                                    <?php while ($sale = mysqli_fetch_assoc($recentSales)): ?>
                                        <li class="list-group-item">
                                            <strong>COP <?php echo number_format($sale['total'], 0, ',', '.'); ?></strong> · <?php echo htmlspecialchars($sale['details'] ?: 'Sin detalle'); ?>
                                            <br>
                                            <small class="text-muted"><?php echo $sale['product_name'] ? htmlspecialchars($sale['product_name']) . ' · ' : ''; ?><?php echo date('d/m/Y H:i', strtotime($sale['sold_at'])); ?></small>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-muted">Sin ventas registradas.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
