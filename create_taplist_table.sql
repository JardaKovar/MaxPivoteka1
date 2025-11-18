CREATE TABLE taplist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number INT,
    brewery VARCHAR(255),
    beer VARCHAR(255),
    alc VARCHAR(50),
    epm VARCHAR(50),
    price_05l DECIMAL(10, 2)
);
