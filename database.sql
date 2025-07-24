-- Database structure for PHP CRUD Admin Dashboard
-- Create database
CREATE DATABASE IF NOT EXISTS admin_crud_db;
USE admin_crud_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15),
    age INT(3),
    address TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create admin table
CREATE TABLE IF NOT EXISTS admins (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
-- First delete existing admin if any, then insert new one
DELETE FROM `admins` WHERE `username` = 'admin';
INSERT INTO `admins`(`username`, `password`, `email`, `created_at`) VALUES 
('admin', '$2y$10$e3B0C44298fc1c149afBf4C8996fb92427ae41e4649b934ca495991b7852b855', 'admin@example.com', NOW());

-- Insert sample users
INSERT INTO users (name, email, phone, age, address, status, registration_date, last_login) VALUES 
('John Doe', 'john@example.com', '1234567890', 25, '123 Main St, City', 'active', '2025-01-01 10:00:00', '2025-07-20 09:30:00'),
('Jane Smith', 'jane@example.com', '0987654321', 30, '456 Oak Ave, Town', 'active', '2025-02-15 14:20:00', '2025-07-22 16:45:00'),
('Bob Johnson', 'bob@example.com', '5551234567', 45, '789 Pine Rd, Village', 'inactive', '2024-12-10 08:15:00', '2025-06-30 11:20:00'),
('Alice Brown', 'alice@example.com', '7778889999', 28, '321 Elm St, County', 'active', '2025-03-20 11:30:00', '2025-07-24 08:15:00'),
('Charlie Wilson', 'charlie@example.com', '4445556666', 55, '654 Maple Dr, District', 'active', '2024-11-05 16:45:00', '2025-07-15 14:30:00');
