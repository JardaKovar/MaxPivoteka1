CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    rental_item VARCHAR(255) NOT NULL,
    rental_period VARCHAR(50) NOT NULL,
    rental_date_from DATE NOT NULL,
    rental_date_to DATE NOT NULL,
    additional_info TEXT,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_rental_date_from (rental_date_from),
    INDEX idx_rental_date_to (rental_date_to),
    INDEX idx_email (email)
);
