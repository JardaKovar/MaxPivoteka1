-- =============================================
-- EXPORT DATABÁZE PRO MAX PIVOTÉKA
-- Pro hosting: InfinityFree.net
-- =============================================
-- 
-- Jak použít:
-- 1. Přihlas se do cPanelu na InfinityFree
-- 2. Otevři phpMyAdmin
-- 3. Vyber databázi (bude vytvořená automaticky)
-- 4. Klikni na "SQL" záložku
-- 5. Zkopíruj celý tento soubor do textového pole
-- 6. Klikni na "Provést" / "Go"
-- =============================================

-- Nejprve vytvoříme tabulky

-- =============================================
-- 1. TABULKA: users (uživatelé)
-- =============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `diary_access` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- 2. TABULKA: active_sessions (aktivní sezení)
-- =============================================
CREATE TABLE IF NOT EXISTS `active_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `session_token` varchar(64) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `login_time` datetime DEFAULT current_timestamp(),
  `last_activity` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- 3. TABULKA: events (akce)
-- =============================================
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(20) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- 4. TABULKA: taplist (pivo na čepu)
-- =============================================
CREATE TABLE IF NOT EXISTS `taplist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) DEFAULT NULL,
  `brewery` varchar(255) DEFAULT NULL,
  `beer` varchar(255) DEFAULT NULL,
  `alc` varchar(50) DEFAULT NULL,
  `epm` varchar(50) DEFAULT NULL,
  `ibu` varchar(50) DEFAULT NULL,
  `ebc` varchar(50) DEFAULT NULL,
  `price_05l` decimal(10,2) DEFAULT NULL,
  `price_03l` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- 5. TABULKA: rentallist (půjčovna)
-- =============================================
CREATE TABLE IF NOT EXISTS `rentallist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) DEFAULT NULL,
  `desc1` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `desc2` varchar(255) DEFAULT NULL,
  `deposit` varchar(50) DEFAULT NULL,
  `day` varchar(50) DEFAULT NULL,
  `weekend` varchar(50) DEFAULT NULL,
  `week` varchar(50) DEFAULT NULL,
  `month` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- 6. TABULKA: activity_logs (logy aktivit)
-- =============================================
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `action` varchar(100) NOT NULL,
  `section` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- 7. TABULKA: login_sessions (historie přihlášení)
-- =============================================
CREATE TABLE IF NOT EXISTS `login_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `action` enum('login','logout') NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- 8. TABULKA: current_activity_logs
-- =============================================
CREATE TABLE IF NOT EXISTS `current_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- 9. TABULKA: reservations (rezervace)
-- =============================================
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `rental_item` varchar(255) DEFAULT NULL,
  `rental_period` varchar(50) DEFAULT NULL,
  `rental_date_from` date DEFAULT NULL,
  `rental_date_to` date DEFAULT NULL,
  `additional_info` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- 10. TABULKA: cenik_images (ceník)
-- =============================================
CREATE TABLE IF NOT EXISTS `cenik_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- 11. TABULKA: gallery (galerie)
-- =============================================
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- VLOŽENÍ VÝCHOZÍCH DAT
-- =============================================

-- Uživatelé (hesla si nastavíš po prvním přihlášení)
INSERT IGNORE INTO `users` (`username`, `password_hash`, `diary_access`, `is_active`) VALUES
('MaxZ', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 1),
('Admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1),
('MaxP', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

-- Události / Akce
INSERT IGNORE INTO `events` (`date`, `title`, `description`) VALUES
('24.12.', 'Vánoční pivní speciály', 'Přijďte ochutnat naše sváteční pivní speciály. V nabídce budou tmavé i světlé vánoční speciály od vybraných pivovarů.'),
('31.12.', 'Silvestrovská ochutnávka', 'Speciální silvestrovská nabídka piv. Připravili jsme pro vás výběr toho nejlepšího z našeho sortimentu.'),
('15.1.', 'Degustace řemeslných piv', 'Řízená degustace vybraných řemeslných piv z českých minipivovarů. Rezervace míst předem nutná.');

-- Pivní lístek (Právě na čepu)
INSERT IGNORE INTO `taplist` (`number`, `brewery`, `beer`, `alc`, `epm`, `ibu`, `ebc`, `price_05l`, `price_03l`) VALUES
(1, 'KAMENICE NAD LIPOU', 'KAMENICKÁ 10', '4,2', '10', '28', '11', 55.00, 39.00),
(2, 'MAXBEER BN', 'EXTRA HOŘKÝ LEŽÁK', '5', '12', '52', '', 59.00, 45.00),
(3, 'HUBERTUS KÁCOV', 'L.P. 1457 SVĚTLÝ LEŽÁK', '4,4', '11', '', '', 49.00, 39.00),
(4, 'FERDINAND BN', 'SVĚTLÝ LEŽÁK PREMIUM', '5', '12', '', '', 49.00, 39.00),
(5, 'PLZEŇSKÝ PRAZDROJ', 'PILSNER URQUELL', '4,4', '11', '', '', 59.00, 45.00);

-- Půjčovna
INSERT IGNORE INTO `rentallist` (`number`, `desc1`, `image`, `desc2`, `deposit`, `day`, `weekend`, `week`, `month`) VALUES
(1, 'GRILY', 'grill.png', 'PLYNOVÝ GRIL', '2000', '200', '400', '1000', '2500'),
(2, 'PÍPY', 'pípa.png', 'PŘENOSNÉ CHLAZENÍ', '3000', '300', '500', '1200', '3000'),
(3, 'PIV. SETY', 'pivset (2).png', 'STŮL + 2 LAVICE', '500', '50', '100', '200', '500'),
(4, 'BOMBY', 'bomba.png', 'VÝČEPNÍ ZAŘÍZENÍ', '200', '20', '40', '100', '200');

-- =============================================
-- HOTOVO!
-- =============================================
