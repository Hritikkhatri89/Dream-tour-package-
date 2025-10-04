CREATE TABLE hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    star INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    room_facilities TEXT,
    services TEXT,
    amenities TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
