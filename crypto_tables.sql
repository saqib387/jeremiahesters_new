-- Cryptocurrencies table
CREATE TABLE IF NOT EXISTS cryptocurrencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    creator_user_id INT,
    name VARCHAR(255),
    symbol VARCHAR(10),
    logo VARCHAR(255),
    description TEXT,
    initial_price DECIMAL(18,8),
    current_price DECIMAL(18,8),
    total_supply BIGINT,
    available_supply BIGINT,
    blockchain_network VARCHAR(50),
    contract_address VARCHAR(255),
    contract_abi TEXT,
    is_verified BOOLEAN DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    creator_fee_percentage DECIMAL(5,2) DEFAULT 5.00,
    platform_fee_percentage DECIMAL(5,2) DEFAULT 2.50,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Crypto wallets table
CREATE TABLE IF NOT EXISTS crypto_wallets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    cryptocurrency_id INT,
    balance BIGINT DEFAULT 0,
    wallet_address VARCHAR(255),
    private_key_encrypted TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Crypto transactions table
CREATE TABLE IF NOT EXISTS crypto_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cryptocurrency_id INT,
    buyer_user_id INT,
    seller_user_id INT NULL,
    type VARCHAR(20),
    amount BIGINT,
    price_per_token DECIMAL(18,8),
    total_price DECIMAL(18,8),
    fee_amount DECIMAL(18,8),
    transaction_hash VARCHAR(255) NULL,
    status VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Crypto revenue shares table
CREATE TABLE IF NOT EXISTS crypto_revenue_shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cryptocurrency_id INT,
    transaction_id INT,
    revenue_amount DECIMAL(18,8),
    distribution_amount DECIMAL(18,8),
    is_distributed BOOLEAN DEFAULT 0,
    distributed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
); 