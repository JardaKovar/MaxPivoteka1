CREATE TABLE rentallist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number INT,
    desc1 VARCHAR(255),
    image VARCHAR(255),
    desc2 VARCHAR(255),
    deposit DECIMAL(10, 2),
    day DECIMAL(10, 2),
    weekend DECIMAL(10, 2),
    week DECIMAL(10, 2),
    month DECIMAL(10, 2)
);
