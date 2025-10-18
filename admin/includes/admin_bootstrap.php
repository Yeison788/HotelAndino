<?php
/**
 * Utilidades comunes para el panel administrativo.
 */
if (!function_exists('admin_column_exists')) {
    function admin_column_exists(mysqli $conn, string $table, string $column): bool
    {
        $sql = sprintf("SHOW COLUMNS FROM `%s` LIKE '%s'", $table, $conn->real_escape_string($column));
        $result = mysqli_query($conn, $sql);
        if ($result === false) {
            return false;
        }
        $exists = mysqli_num_rows($result) > 0;
        mysqli_free_result($result);
        return $exists;
    }
}

if (!function_exists('admin_available_permissions')) {
    function admin_available_permissions(): array
    {
        return [
            'dashboard'             => 'Panel principal',
            'reservas'              => 'Reservas',
            'pagos'                 => 'Pagos y facturación',
            'habitaciones'          => 'Habitaciones',
            'personal'              => 'Gestión de personal',
            'registros.summary'     => 'Resumen de registros',
            'registros.rooms'       => 'Registrar habitación',
            'registros.room-types'  => 'Registrar tipo de habitación',
            'registros.admins'      => 'Registrar administrativos',
            'registros.products'    => 'Registrar producto',
            'registros.sales'       => 'Registrar venta',
            'registros.pricing'     => 'Tarifas de habitaciones',
            'estado_habitaciones'   => 'Estado de habitaciones',
        ];
    }
}

if (!function_exists('admin_default_role_permissions')) {
    function admin_default_role_permissions(string $role): array
    {
        $role = strtolower(trim($role));
        switch ($role) {
            case 'recepcionista':
                return ['dashboard', 'reservas', 'pagos', 'habitaciones', 'registros.summary', 'estado_habitaciones'];
            case 'doorman':
            case 'portero':
                return ['dashboard', 'estado_habitaciones'];
            case 'limpieza':
                return ['habitaciones', 'estado_habitaciones'];
            default:
                return ['dashboard'];
        }
    }
}

if (!function_exists('admin_default_room_rates')) {
    function admin_default_room_rates(): array
    {
        return [
            'Habitación Sencilla'  => 60000.00,
            'Habitación Doble'     => 90000.00,
            'Habitación Múltiple'  => 120000.00,
            'Habitación Suite'     => 150000.00,
        ];
    }
}

if (!function_exists('ensureEmpStructure')) {
    function ensureEmpStructure(mysqli $conn): void
    {
        if (!admin_column_exists($conn, 'emp_login', 'FullName')) {
            mysqli_query($conn, "ALTER TABLE emp_login ADD COLUMN FullName VARCHAR(120) NULL AFTER Emp_Password");
        }
        if (!admin_column_exists($conn, 'emp_login', 'Role')) {
            mysqli_query($conn, "ALTER TABLE emp_login ADD COLUMN Role VARCHAR(60) NULL AFTER FullName");
        }
        if (!admin_column_exists($conn, 'emp_login', 'Permissions')) {
            mysqli_query($conn, "ALTER TABLE emp_login ADD COLUMN Permissions TEXT NULL AFTER Role");
        }
        if (!admin_column_exists($conn, 'emp_login', 'IsSuperAdmin')) {
            mysqli_query($conn, "ALTER TABLE emp_login ADD COLUMN IsSuperAdmin TINYINT(1) NOT NULL DEFAULT 0 AFTER Permissions");
        }
        if (!admin_column_exists($conn, 'emp_login', 'CreatedAt')) {
            mysqli_query($conn, "ALTER TABLE emp_login ADD COLUMN CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER IsSuperAdmin");
        }
        if (!admin_column_exists($conn, 'emp_login', 'AvatarPath')) {
            mysqli_query($conn, "ALTER TABLE emp_login ADD COLUMN AvatarPath VARCHAR(255) NULL AFTER CreatedAt");
        }

        $allPermissions = json_encode(array_keys(admin_available_permissions()), JSON_UNESCAPED_UNICODE);
        $stmt = $conn->prepare("UPDATE emp_login SET Permissions = ?, Role = 'Super Administrador', FullName = IF(FullName IS NULL OR FullName = '', 'Administrador General', FullName), IsSuperAdmin = 1 WHERE Emp_Email = 'admin@hotelandino.com'");
        if ($stmt) {
            $stmt->bind_param('s', $allPermissions);
            $stmt->execute();
            $stmt->close();
        }
    }
}

if (!function_exists('ensureRoomRates')) {
    function ensureRoomRates(mysqli $conn): void
    {
        mysqli_query(
            $conn,
            "CREATE TABLE IF NOT EXISTS room_rates (" .
            " room_type VARCHAR(80) NOT NULL PRIMARY KEY," .
            " base_price DECIMAL(10,2) NOT NULL DEFAULT 0," .
            " updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $defaults = admin_default_room_rates();
        $stmt = $conn->prepare("INSERT INTO room_rates (room_type, base_price) VALUES (?, ?) ON DUPLICATE KEY UPDATE base_price = base_price");
        if ($stmt) {
            foreach ($defaults as $type => $price) {
                $stmt->bind_param('sd', $type, $price);
                $stmt->execute();
            }
            $stmt->close();
        }
    }
}

if (!function_exists('ensureStaffStructure')) {
    function ensureStaffStructure(mysqli $conn): void
    {
        $columns = [
            'category'        => "ALTER TABLE staff ADD COLUMN category ENUM('administrativo','operativo') NOT NULL DEFAULT 'operativo' AFTER work",
            'admin_email'     => "ALTER TABLE staff ADD COLUMN admin_email VARCHAR(190) NULL UNIQUE AFTER category",
            'document_number' => "ALTER TABLE staff ADD COLUMN document_number VARCHAR(40) NULL AFTER admin_email",
            'email'           => "ALTER TABLE staff ADD COLUMN email VARCHAR(120) NULL AFTER document_number",
            'phone'           => "ALTER TABLE staff ADD COLUMN phone VARCHAR(40) NULL AFTER email",
            'hire_date'       => "ALTER TABLE staff ADD COLUMN hire_date DATE NULL AFTER phone",
            'salary'          => "ALTER TABLE staff ADD COLUMN salary DECIMAL(12,2) NULL AFTER hire_date",
            'notes'           => "ALTER TABLE staff ADD COLUMN notes TEXT NULL AFTER salary",
        ];

        foreach ($columns as $column => $alterSql) {
            if (!admin_column_exists($conn, 'staff', $column)) {
                mysqli_query($conn, $alterSql);
            }
        }
    }
}

if (!function_exists('admin_is_ajax_request')) {
    function admin_is_ajax_request(): bool
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return stripos($accept, 'application/json') !== false;
    }
}

if (!function_exists('admin_room_base_price')) {
    function admin_room_base_price(mysqli $conn, string $roomType): float
    {
        static $cache = [];
        if (isset($cache[$roomType])) {
            return $cache[$roomType];
        }

        $stmt = $conn->prepare("SELECT base_price FROM room_rates WHERE room_type = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('s', $roomType);
            if ($stmt->execute()) {
                $stmt->bind_result($price);
                if ($stmt->fetch()) {
                    $cache[$roomType] = (float) $price;
                    $stmt->close();
                    return $cache[$roomType];
                }
            }
            $stmt->close();
        }

        $defaults = admin_default_room_rates();
        $cache[$roomType] = $defaults[$roomType] ?? 0.0;
        return $cache[$roomType];
    }
}

if (!function_exists('admin_refresh_session')) {
    function admin_refresh_session(mysqli $conn, string $email): void
    {
        if ($email === '') {
            return;
        }

        $stmt = $conn->prepare("SELECT Emp_Email, FullName, Role, Permissions, IsSuperAdmin, AvatarPath FROM emp_login WHERE Emp_Email = ? LIMIT 1");
        if (!$stmt) {
            return;
        }
        $stmt->bind_param('s', $email);
        if ($stmt->execute()) {
            $stmt->bind_result($empEmail, $fullName, $role, $permissionsJson, $isSuper, $avatarPath);
            if ($stmt->fetch()) {
                $perms = [];
                if ($permissionsJson) {
                    $decoded = json_decode($permissionsJson, true);
                    if (is_array($decoded)) {
                        $perms = array_values(array_unique(array_map('strval', $decoded)));
                    }
                }
                if ($empEmail === 'admin@hotelandino.com') {
                    $isSuper = 1;
                    $perms = array_keys(admin_available_permissions());
                }
                $_SESSION['adminmail'] = $empEmail;
                $_SESSION['admin_name'] = $fullName ?: $empEmail;
                $_SESSION['admin_role'] = $role ?: '';
                $_SESSION['admin_permissions'] = $perms;
                $_SESSION['admin_is_super'] = (bool)$isSuper;
                $_SESSION['admin_avatar'] = $avatarPath ?: '';
            }
        }
        $stmt->close();
    }
}

if (!function_exists('admin_user_can')) {
    function admin_user_can(string $permission): bool
    {
        if (!empty($_SESSION['admin_is_super'])) {
            return true;
        }
        $perms = $_SESSION['admin_permissions'] ?? [];
        return in_array($permission, $perms, true);
    }
}

if (!function_exists('admin_require_permission')) {
    function admin_require_permission(string $permission): void
    {
        if (admin_user_can($permission)) {
            return;
        }
        http_response_code(403);
        echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Acceso restringido</title>';
        echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"></head>';
        echo '<body class="bg-light"><div class="container py-5"><div class="alert alert-danger shadow-sm">';
        echo '<h1 class="h4">Acceso restringido</h1><p class="mb-0">No cuentas con permisos suficientes para ingresar a esta sección.</p>';
        echo '</div></div></body></html>';
        exit;
    }
}

if (!function_exists('admin_current_employee')) {
    function admin_current_employee(): array
    {
        return [
            'email'  => $_SESSION['adminmail'] ?? '',
            'name'   => $_SESSION['admin_name'] ?? '',
            'role'   => $_SESSION['admin_role'] ?? '',
            'avatar' => $_SESSION['admin_avatar'] ?? '',
        ];
    }
}

if (!function_exists('admin_permissions_for_display')) {
    function admin_permissions_for_display(): array
    {
        $all = admin_available_permissions();
        $grouped = [
            'Panel principal'       => ['dashboard'],
            'Operación diaria'      => ['reservas', 'pagos', 'habitaciones', 'estado_habitaciones'],
            'Gestión interna'       => ['personal'],
            'Registros'             => ['registros.summary', 'registros.rooms', 'registros.room-types', 'registros.admins', 'registros.products', 'registros.sales', 'registros.pricing'],
        ];
        $result = [];
        foreach ($grouped as $section => $keys) {
            $items = [];
            foreach ($keys as $key) {
                if (isset($all[$key])) {
                    $items[$key] = $all[$key];
                }
            }
            if (!empty($items)) {
                $result[$section] = $items;
            }
        }
        return $result;
    }
}

if (!function_exists('admin_has_records_access')) {
    function admin_has_records_access(): bool
    {
        if (!empty($_SESSION['admin_is_super'])) {
            return true;
        }
        $perms = $_SESSION['admin_permissions'] ?? [];
        foreach ($perms as $perm) {
            if (strpos($perm, 'registros.') === 0) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('admin_first_records_view')) {
    function admin_first_records_view(): string
    {
        $views = [
            'summary'      => 'registros.summary',
            'rooms'        => 'registros.rooms',
            'room-types'   => 'registros.room-types',
            'admin-staff'  => 'registros.admins',
            'products'     => 'registros.products',
            'sales'        => 'registros.sales',
            'pricing'      => 'registros.pricing',
        ];
        foreach ($views as $view => $permission) {
            if (admin_user_can($permission)) {
                return $view;
            }
        }
        return 'summary';
    }
}
}

