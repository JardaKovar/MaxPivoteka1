CREATE TABLE IF NOT EXISTS price_list (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_filename VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert or update the current price list image
DELIMITER //
CREATE PROCEDURE upsert_price_list_image(IN img_filename VARCHAR(255))
BEGIN
    IF EXISTS (SELECT 1 FROM price_list WHERE id = 1) THEN
        UPDATE price_list SET image_filename = img_filename, uploaded_at = CURRENT_TIMESTAMP WHERE id = 1;
    ELSE
        INSERT INTO price_list (id, image_filename) VALUES (1, img_filename);
    END IF;
END //
DELIMITER ;
