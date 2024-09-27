-- Create table for users
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,  -- Store hashed passwords
    email VARCHAR(100) NOT NULL UNIQUE,
    balance DECIMAL(16, 8) DEFAULT 0.00000000, -- Bitcoin balance (up to 8 decimal places)
    bitcoin_rpc_username VARCHAR(50) NOT NULL,  -- Bitcoin API username
    bitcoin_rpc_password VARCHAR(255) NOT NULL, -- Bitcoin API password (store hashed or encrypted for security)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create table for transactions
CREATE TABLE transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(16, 8) NOT NULL, -- Transaction amount in BTC
    transaction_type ENUM('credit', 'debit') NOT NULL, -- Type of transaction
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create table for account settings (optional)
CREATE TABLE account_settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    setting_key VARCHAR(50) NOT NULL,
    setting_value VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Insert a sample user (with a hashed password)
-- You should hash the password in your application code (e.g., using bcrypt)
INSERT INTO users (username, password_hash, email, bitcoin_rpc_username, bitcoin_rpc_password, balance)
VALUES (
    'root', 
    '$2y$10$7aV7g5K1jQZ7b4JPSm7j4eOFlRbfF9PH1HQhEee9PfQ8lLqk/VS7y', -- Example hash for 'root'
    'root@example.com', 
    'btc_rpc_user', 
    'btc_rpc_pass', 
    0.10000000
);

-- Insert a sample transaction
INSERT INTO transactions (user_id, amount, transaction_type, status)
VALUES (1, 0.05000000, 'debit', 'completed');
