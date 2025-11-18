-- Create table for storing current state activity logs (max 24 hours retention)
CREATE TABLE IF NOT EXISTS current_activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    section VARCHAR(50) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Index for quick cleanup by last_updated
CREATE INDEX idx_last_updated ON current_activity_logs(last_updated);
