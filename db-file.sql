create database agriconnect;
use agriconnect;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('Farmer', 'User') NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE users
MODIFY COLUMN role ENUM('Farmer', 'User', 'Admin') NOT NULL;


select*from users;


CREATE TABLE purchases (
    purchase_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    
    product_name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,

    buyer_name VARCHAR(100) NOT NULL,
    delivery_address TEXT NOT NULL,
    contact_number VARCHAR(15) NOT NULL,

    payment_mode ENUM('COD', 'UPI', 'Card') NOT NULL,
    upi_id VARCHAR(100),
    masked_card_number VARCHAR(20),
    
    purchase_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
select*from purchases;


CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,          -- e.g., vegetables, fruits, seeds, etc.
    seller_type ENUM('user', 'farmer') NOT NULL, -- to distinguish who added the product
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO products (name, price, image_path, category, seller_type) VALUES
('Tomato', 40, 'tomtos.jpg', 'vegetables', 'user'),
('Potato', 30, 'potato.jpg', 'vegetables', 'user'),
('Carrots', 60, 'carrots.jpg', 'vegetables', 'user'),
('Capsicum', 60, 'capsicum1.jpeg', 'vegetables', 'user'),
('Onion', 60, 'onion.jpeg', 'vegetables', 'user'),
('Beetroots', 60, 'beetroot-1.jpg', 'vegetables', 'user'),
('Spinach', 60, 'spinach.jpg', 'vegetables', 'user'),
('Fenugreek (Methi)', 60, 'MethiFenugreek.jpg', 'vegetables', 'user'),
('Ladyfinger', 60, 'ladyfinger.jpg', 'vegetables', 'user'),
('Cabbage', 60, 'cabage.jpeg', 'vegetables', 'user'),

('Apple', 120, 'apple.jpg', 'fruits', 'user'),
('Banana', 50, 'banana.jpg', 'fruits', 'user'),
('Watermelon', 45, 'watermelons.jpg', 'fruits', 'user'),
('Strawberries', 150, 'strawberies.png', 'fruits', 'user'),
('Grapes', 60, 'grapes.jpg', 'fruits', 'user'),

('Watermelons seeds', 500, 'watermelonseed.jpg', 'Seeds', 'farmer'),
('Tomatoes seeds', 450, 'tomatoseeds.jpg', 'Seeds', 'farmer'),
('Coriander seeds', 450, 'coriender.jpg', 'Seeds', 'farmer'),
('Cabbage seeds', 450, 'cabage.jpg', 'Seeds', 'farmer'),

('Tractor', 500000, 'tractor.jpg', 'Machinery', 'farmer'),
('Plough', 4500, 'plough.png', 'Machinery', 'farmer'),
('Knapsack', 4500, 'knapsack.jpg', 'Machinery', 'farmer'),
('Sickle', 4500, 'khurpa1.jpg', 'Machinery', 'farmer'),
('Pickaxe', 4500, 'pickaxes.png', 'Machinery', 'farmer'),

('Vegetable Fertilizer', 500, 'vegf.jpg', 'Fertilizers', 'farmer'),
('Fruit Fertilizer', 450, 'fruit.jpg', 'Fertilizers', 'farmer'),
('Organic fertilizer', 450, 'organic.jpg', 'Fertilizers', 'farmer'),
('Ammonium sulphate (chemical fertilizer)', 450, 'chemicalf.jpg', 'Fertilizers', 'farmer');


select * from purchases;
select * from products;
select * from users;

CREATE TABLE subsidies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    amount TEXT NOT NULL,
    description TEXT NOT NULL,
    eligibility TEXT NOT NULL,
    category VARCHAR(255),
    documents TEXT,
    link VARCHAR(500)  -- Add actual URL later as needed
);



INSERT INTO subsidies (name, amount, description, eligibility, category, documents, link)
VALUES (
    'Group Farming Scheme(Gat sheti Yojana)',
    '6000',
    'Guidance on the crop pattern and farming techniques.to help individual farmers to collectively shoulder the investment expenditure.',
    'At least ten farmers. Small and marginal farmers. Residents of Maharashtra.',
    'Agricultural Group farming Schemes.',
    'Aadhar, Bank Details, Land Ownership Proof, Joint Farming Agreement',
    'https://example.com/gat-sheti-yojana'
);

describe users;
SELECT * FROM users WHERE email = 'ishan@abc.com';
select * from subsidies;

