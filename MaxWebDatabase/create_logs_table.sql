-- Active: 1751836717764@@127.0.0.1@3306
-- Create logs table for tracking user activities and changes
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    action VARCHAR(100) NOT NULL,
    section VARCHAR(50) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create login_sessions table for tracking login/logout activities
CREATE TABLE IF NOT EXISTS login_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    action ENUM('login', 'logout') NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    session_id VARCHAR(128),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert some sample data for demonstration
INSERT INTO activity_logs (username, action, section, details, ip_address) VALUES
();

INSERT INTO login_sessions (username, action, ip_address, session_id) VALUES
('MaxP', 'login', '127.0.0.1', 'sess_123456789'),
('MaxP', 'logout', '127.0.0.1', 'sess_123456789'),
('MaxP', 'login', '127.0.0.1', 'sess_987654321');
