-- ======================================================
-- Esquema: hotelandino  (estructura + seed habitaciones + admin)
-- ======================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `hotelandino`;
CREATE DATABASE IF NOT EXISTS `hotelandino`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `hotelandino`;

-- (Opcional / comentar si tu hosting no permite)
-- -- Crear usuario MySQL
-- DROP USER IF EXISTS 'hotelandino_user'@'%';
-- CREATE USER IF NOT EXISTS 'hotelandino_user'@'%' IDENTIFIED BY 'password';
-- GRANT ALL PRIVILEGES ON hotelandino.* TO 'hotelandino_user'@'%';

-- ======================================================
-- Tabla: signup (usuarios)
-- ======================================================
CREATE TABLE `signup` (
  `UserID` INT NOT NULL AUTO_INCREMENT,
  `Username` VARCHAR(80) NOT NULL,
  `Email` VARCHAR(190) NOT NULL,
  `Password` VARCHAR(255) NOT NULL,     -- recomendado usar password_hash()
  `OnboardingDone` TINYINT(1) NOT NULL DEFAULT 0,
  `RadiusKm` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `BudgetLevel` TINYINT UNSIGNED NOT NULL DEFAULT 2,
  `PrefUpdatedAt` DATETIME NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `uniq_signup_email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- Tabla: emp_login (admins) + admin por defecto
-- ======================================================
CREATE TABLE `emp_login` (
  `EmpID` INT NOT NULL AUTO_INCREMENT,
  `Emp_Email` VARCHAR(190) NOT NULL,
  `Emp_Password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`EmpID`),
  UNIQUE KEY `uniq_emp_email` (`Emp_Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 🔐 Admin por defecto (como lo tenías)
INSERT INTO `emp_login` (`Emp_Email`, `Emp_Password`) VALUES
('admin@hotelandino.com', '1234');

-- ======================================================
-- Tabla: room (habitaciones)
-- ======================================================
CREATE TABLE `room` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `floor` TINYINT NOT NULL,                 -- 1..4
  `room_number` VARCHAR(10) NOT NULL,       -- 101..113 / 201..213 / etc.
  `type` VARCHAR(50) NOT NULL,
  `bedding` VARCHAR(50) NOT NULL,
  `status` ENUM('Disponible','Reservada','Limpieza','Ocupada') NOT NULL DEFAULT 'Disponible',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_room_number` (`room_number`),
  KEY `idx_room_floor` (`floor`),
  KEY `idx_room_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ✅ Seed de habitaciones (Pisos 1–4, 13 por piso)
INSERT INTO `room` (`floor`, `room_number`, `type`, `bedding`, `status`) VALUES
-- Piso 1 (101–113)
(1, '101', 'Habitación Sencilla', '1', 'Disponible'),
(1, '102', 'Habitación Sencilla', '1', 'Disponible'),
(1, '103', 'Habitación Sencilla', '1', 'Disponible'),
(1, '104', 'Habitación Sencilla', '1', 'Disponible'),
(1, '105', 'Habitación Doble',    '2', 'Disponible'),
(1, '106', 'Habitación Doble',    '2', 'Disponible'),
(1, '107', 'Habitación Doble',    '2', 'Disponible'),
(1, '108', 'Habitación Doble',    '2', 'Disponible'),
(1, '109', 'Habitación Múltiple', '3', 'Disponible'),
(1, '110', 'Habitación Múltiple', '3', 'Disponible'),
(1, '111', 'Habitación Múltiple', '4', 'Disponible'),
(1, '112', 'Habitación Múltiple', '5', 'Disponible'),
(1, '113', 'Habitación Múltiple', '6', 'Disponible'),
-- Piso 2 (201–213)
(2, '201', 'Habitación Sencilla', '1', 'Disponible'),
(2, '202', 'Habitación Sencilla', '1', 'Disponible'),
(2, '203', 'Habitación Sencilla', '1', 'Disponible'),
(2, '204', 'Habitación Sencilla', '1', 'Disponible'),
(2, '205', 'Habitación Doble',    '2', 'Disponible'),
(2, '206', 'Habitación Doble',    '2', 'Disponible'),
(2, '207', 'Habitación Doble',    '2', 'Disponible'),
(2, '208', 'Habitación Doble',    '2', 'Disponible'),
(2, '209', 'Habitación Múltiple', '3', 'Disponible'),
(2, '210', 'Habitación Múltiple', '3', 'Disponible'),
(2, '211', 'Habitación Múltiple', '4', 'Disponible'),
(2, '212', 'Habitación Múltiple', '5', 'Disponible'),
(2, '213', 'Habitación Múltiple', '6', 'Disponible'),
-- Piso 3 (301–313)
(3, '301', 'Habitación Sencilla', '1', 'Disponible'),
(3, '302', 'Habitación Sencilla', '1', 'Disponible'),
(3, '303', 'Habitación Sencilla', '1', 'Disponible'),
(3, '304', 'Habitación Sencilla', '1', 'Disponible'),
(3, '305', 'Habitación Doble',    '2', 'Disponible'),
(3, '306', 'Habitación Doble',    '2', 'Disponible'),
(3, '307', 'Habitación Doble',    '2', 'Disponible'),
(3, '308', 'Habitación Doble',    '2', 'Disponible'),
(3, '309', 'Habitación Múltiple', '3', 'Disponible'),
(3, '310', 'Habitación Múltiple', '3', 'Disponible'),
(3, '311', 'Habitación Múltiple', '4', 'Disponible'),
(3, '312', 'Habitación Múltiple', '5', 'Disponible'),
(3, '313', 'Habitación Múltiple', '6', 'Disponible'),
-- Piso 4 (401–413)
(4, '401', 'Habitación Sencilla', '1', 'Disponible'),
(4, '402', 'Habitación Sencilla', '1', 'Disponible'),
(4, '403', 'Habitación Sencilla', '1', 'Disponible'),
(4, '404', 'Habitación Sencilla', '1', 'Disponible'),
(4, '405', 'Habitación Doble',    '2', 'Disponible'),
(4, '406', 'Habitación Doble',    '2', 'Disponible'),
(4, '407', 'Habitación Doble',    '2', 'Disponible'),
(4, '408', 'Habitación Doble',    '2', 'Disponible'),
(4, '409', 'Habitación Múltiple', '3', 'Disponible'),
(4, '410', 'Habitación Múltiple', '3', 'Disponible'),
(4, '411', 'Habitación Múltiple', '4', 'Disponible'),
(4, '412', 'Habitación Múltiple', '5', 'Disponible'),
(4, '413', 'Habitación Múltiple', '6', 'Disponible');

-- ======================================================
-- Tabla: room_types (catálogo editable desde admin)
-- ======================================================
CREATE TABLE `room_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_room_types_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `room_types` (`name`, `description`) VALUES
('Habitación Sencilla', 'Pensada para viajeros que buscan comodidad con todos los esenciales para una persona.'),
('Habitación Doble', 'Perfecta para parejas o amigos que desean compartir y disfrutar de dos espacios de descanso.'),
('Habitación Múltiple', 'Nuestra opción más amplia para familias o grupos, con capacidad flexible hasta seis huéspedes.')
ON DUPLICATE KEY UPDATE
  `description` = VALUES(`description`);

-- ======================================================
-- Tabla: products (inventario simple para ventas)
-- ======================================================
CREATE TABLE `products` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_products_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- Tabla: sales (registro de ventas internas)
-- ======================================================
CREATE TABLE `sales` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product_id` INT DEFAULT NULL,
  `details` VARCHAR(190) DEFAULT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `sold_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sales_product` (`product_id`),
  CONSTRAINT `fk_sales_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- Tabla: room_stays (detalle de huéspedes por habitación)
-- ======================================================
CREATE TABLE `room_stays` (
  `room_id` INT NOT NULL,
  `guest_id` VARCHAR(40) NOT NULL,
  `guest_name` VARCHAR(120) NOT NULL,
  `nationality` VARCHAR(80) NOT NULL,
  `check_in_date` DATE NOT NULL,
  `check_in_time` TIME NOT NULL,
  `check_out_date` DATE NOT NULL,
  `receptionist_email` VARCHAR(190) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`room_id`),
  CONSTRAINT `fk_room_stays_room` FOREIGN KEY (`room_id`) REFERENCES `room`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- Tabla: roombook (reservas)
-- ======================================================
CREATE TABLE `roombook` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `room_id` INT NULL,
  `Name` VARCHAR(50) NOT NULL,
  `Email` VARCHAR(50) NOT NULL,
  `Country` VARCHAR(30) NOT NULL,
  `Phone` VARCHAR(30) NOT NULL,
  `RoomType` VARCHAR(30) NOT NULL,
  `Bed` VARCHAR(30) NOT NULL,
  `Meal` VARCHAR(30) NOT NULL,
  `NoofRoom` VARCHAR(30) NOT NULL,
  `cin` DATE NOT NULL,
  `cout` DATE NOT NULL,
  `nodays` INT NOT NULL,
  `stat` VARCHAR(30) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_roombook_room_id` (`room_id`),
  CONSTRAINT `fk_roombook_room`
    FOREIGN KEY (`room_id`) REFERENCES `room`(`id`)
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- Tabla: payment (pagos)
-- ======================================================
CREATE TABLE `payment` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(30) NOT NULL,
  `Email` VARCHAR(30) NOT NULL,
  `RoomType` VARCHAR(30) NOT NULL,
  `Bed` VARCHAR(30) NOT NULL,
  `NoofRoom` INT NOT NULL,
  `cin` DATE NOT NULL,
  `cout` DATE NOT NULL,
  `noofdays` INT NOT NULL,
  `roomtotal` DECIMAL(10,2) NOT NULL,
  `bedtotal` DECIMAL(10,2) NOT NULL,
  `meal` VARCHAR(30) NOT NULL,
  `mealtotal` DECIMAL(10,2) NOT NULL,
  `finaltotal` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- Tabla: staff (personal)
-- ======================================================
CREATE TABLE `staff` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `work` VARCHAR(80) NOT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- Tabla: preference_catalog (catálogo opcional)
-- ======================================================
CREATE TABLE `preference_catalog` (
  `pref_key` VARCHAR(30) NOT NULL,
  `label` VARCHAR(50) NOT NULL,
  `place_types` VARCHAR(255) NOT NULL,   -- tipos de Google Places separados por coma
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`pref_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Semillas de catálogo (opcional)
INSERT INTO `preference_catalog` (`pref_key`, `label`, `place_types`, `active`) VALUES
('nature',    'Naturaleza',        'park,tourist_attraction',                    1),
('museums',   'Museos / Arte',     'museum,art_gallery',                         1),
('food',      'Gastronomía',       'restaurant,cafe',                            1),
('nightlife', 'Vida nocturna',     'bar,night_club',                             1),
('shopping',  'Compras',           'shopping_mall,department_store',             1),
('family',    'Familiar / Kids',   'zoo,aquarium,amusement_park',                1),
('wellness',  'Wellness / Spa',    'spa,gym',                                    1),
('sports',    'Deportes',          'stadium',                                    1),
('photo',     'Spots para fotos',  'tourist_attraction,park,point_of_interest', 1)
ON DUPLICATE KEY UPDATE
  `label` = VALUES(`label`),
  `place_types` = VALUES(`place_types`),
  `active` = VALUES(`active`);

-- ======================================================
-- Tabla: user_preferences (catálogo elegido por usuario)
-- ======================================================
CREATE TABLE `user_preferences` (
  `UserID` INT NOT NULL,
  `pref_key` VARCHAR(30) NOT NULL,
  `weight` TINYINT UNSIGNED NOT NULL DEFAULT 2,  -- 1..3
  PRIMARY KEY (`UserID`, `pref_key`),
  KEY `idx_up_pref_key` (`pref_key`),
  CONSTRAINT `fk_up_user`
    FOREIGN KEY (`UserID`) REFERENCES `signup`(`UserID`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_up_pref`
    FOREIGN KEY (`pref_key`) REFERENCES `preference_catalog`(`pref_key`)
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- Tabla: user_hidden_places (lugares ocultos por usuario)
-- ======================================================
CREATE TABLE `user_hidden_places` (
  `UserID` INT NOT NULL,
  `place_id` VARCHAR(128) NOT NULL,     -- Google Place ID
  PRIMARY KEY (`UserID`, `place_id`),
  CONSTRAINT `fk_uhp_user`
    FOREIGN KEY (`UserID`) REFERENCES `signup`(`UserID`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- Tabla: user_interests (gustos libres del onboarding/modal)
-- ======================================================
CREATE TABLE `user_interests` (
  `ID` BIGINT NOT NULL AUTO_INCREMENT,
  `UserID` INT NOT NULL,
  `Interest` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `uniq_user_interest` (`UserID`, `Interest`),
  KEY `idx_ui_userid` (`UserID`),
  CONSTRAINT `fk_ui_user`
    FOREIGN KEY (`UserID`) REFERENCES `signup`(`UserID`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- Sugerencia PHP (no es SQL):
--   en config.php usar: mysqli_set_charset($conn, 'utf8mb4');
-- ======================================================

-- Fin
