CREATE DATABASE dress_request_db;

USE dress_request_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user'
);

-- Insert Admin (default login)
INSERT INTO users (name, email, password, role) 
VALUES ('Admin', 'admin@gmail.com', SHA2('admin123', 256), 'admin');

-- dress_requests table
USE dress_request_db;
CREATE TABLE dress_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    image VARCHAR(255) NOT NULL,
    size ENUM('S', 'M', 'L', 'XL', 'XXL') NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    contact_no VARCHAR(15) NOT NULL,
    details TEXT NOT NULL,
    timeline ENUM('1 week', '2 weeks', '3 weeks') NOT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'accepted', 'rejected','delivered','received') DEFAULT 'pending';
);

UPDATE dress_requests
SET image = REPLACE(image, 'C:/xampp/htdocs/', '')
WHERE image LIKE 'C:/xampp/htdocs/uploads/%';

ALTER TABLE dress_requests ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP;

-- feedback table
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread'
);

USE dress_request_db;
ALTER TABLE users
ADD COLUMN is_verified TINYINT(1) DEFAULT 0,
ADD COLUMN verification_token VARCHAR(255) DEFAULT NULL;