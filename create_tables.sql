CREATE DATABASE IF NOT EXISTS `if0_40456836_st_print` 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_general_ci;

USE `if0_40456836_st_print`;

--------------------------------------------------------
-- ADMIN TABLE
--------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(191) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    fullname VARCHAR(191),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin (password: admin123)
INSERT INTO admin_users (username, password_hash, fullname)
VALUES (
    'admin',
    '$2y$10$Xz8gP9rsoN4kE1L7OQ7hEOm1Wjz4aREaJ9dUGuX0cP3p2Q4xQeZ7W',
    'Site Administrator'
)
ON DUPLICATE KEY UPDATE username = username;

--------------------------------------------------------
-- ORDERS TABLE
--------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    department VARCHAR(100) NOT NULL,
    year_level VARCHAR(100) NOT NULL,
    print_type VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    design_option VARCHAR(255),
    description TEXT,
    status ENUM('pending','sold','returned','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--------------------------------------------------------
-- SELLERS TABLE
--------------------------------------------------------
CREATE TABLE IF NOT EXISTS sellers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    commission_rate DECIMAL(5,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--------------------------------------------------------
-- SELLER ITEMS TABLE
--------------------------------------------------------
CREATE TABLE IF NOT EXISTS seller_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    item_code VARCHAR(150) NOT NULL UNIQUE,
    product_name VARCHAR(200) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    quantity INT NOT NULL DEFAULT 0,
    sold_count INT NOT NULL DEFAULT 0,
    status ENUM('available','low','out') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE CASCADE
);

--------------------------------------------------------
-- SELLER SALES TABLE
--------------------------------------------------------
CREATE TABLE IF NOT EXISTS seller_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES seller_items(id) ON DELETE CASCADE
);

--------------------------------------------------------
-- INDEXES
--------------------------------------------------------
CREATE INDEX idx_orders_name ON orders(name);
CREATE INDEX idx_orders_year_level ON orders(year_level);
CREATE INDEX idx_orders_department ON orders(department);
CREATE INDEX idx_seller_items_code ON seller_items(item_code);
CREATE INDEX idx_seller_sales_seller ON seller_sales(seller_id);
CREATE INDEX idx_seller_sales_item ON seller_sales(item_id);
