-- Create gallery management table
CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT DEFAULT 0,
    mime_type VARCHAR(100) DEFAULT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'deleted') DEFAULT 'active',
    created_by VARCHAR(100) DEFAULT 'admin',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_filename (filename),
    INDEX idx_status (status),
    INDEX idx_upload_date (upload_date)
);

-- Create gallery operations log table
CREATE TABLE IF NOT EXISTS gallery_operations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operation_type ENUM('upload', 'delete', 'change', 'rename') NOT NULL,
    old_filename VARCHAR(255) DEFAULT NULL,
    new_filename VARCHAR(255) DEFAULT NULL,
    file_path VARCHAR(500) DEFAULT NULL,
    operation_details TEXT DEFAULT NULL,
    user_id VARCHAR(100) DEFAULT 'admin',
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    operation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('success', 'failed', 'pending') DEFAULT 'pending',
    error_message TEXT DEFAULT NULL,
    INDEX idx_operation_type (operation_type),
    INDEX idx_operation_date (operation_date),
    INDEX idx_status (status)
);

-- Insert existing gallery images into the database
INSERT IGNORE INTO gallery_images (filename, original_name, file_path, status) VALUES
();
