-- Database Schema for Melody Masters Instrument Shop

CREATE DATABASE IF NOT EXISTS melody_masters;
USE melody_masters;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    address TEXT,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    category_id INT,
    image_url VARCHAR(255),
    is_digital BOOLEAN DEFAULT FALSE,
    file_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_cost DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE SET NULL
);

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    user_id INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Insert Sample Categories
INSERT INTO categories (name, description) VALUES 
('Guitars', 'Acoustic, Electric, and Bass Guitars'),
('Keyboards', 'Pianos, Synthesizers, and MIDI Controllers'),
('Drums & Percussion', 'Drum Kits, Cymbals, and Electronic Drums'),
('Wind Instruments', 'Flutes, Saxophones, and Trumpets'),
('String Instruments', 'Violins, Cellos, and Ukuleles'),
('Accessories', 'Cables, Stands, and Cases'),
('Digital Sheet Music', 'Downloadable Sheet Music used by professionals');

-- Insert Sample Products
INSERT INTO products (name, description, price, stock_quantity, category_id, image_url, is_digital) VALUES 
('Fender Stratocaster', 'Classic electric guitar with versatile sound.', 699.00, 10, 1, 'assets/images/guitar_sample.jpg', 0),
('Yamaha P-45 Digital Piano', 'Compact and stylish digital piano.', 450.00, 5, 2, 'assets/images/piano_sample.jpg', 0),
('Pearl Export Drum Kit', 'The best-selling drum set of all time.', 800.00, 3, 3, 'assets/images/drum_sample.jpg', 0),
('Beethoven Symphony No. 9 Sheet Music', 'Full orchestral score in PDF format.', 15.00, 999, 7, 'assets/images/sheet_music_sample.jpg', 1);
