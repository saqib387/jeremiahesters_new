-- Sample Cryptocurrency Data SQL Script
-- This script will add sample cryptocurrency data to your database
-- Make sure to replace 'YOUR_USER_ID' with an actual user ID from your database

-- Find the first user's ID
SET @user_id = (SELECT id FROM users ORDER BY id LIMIT 1);

-- JustCoin
INSERT INTO cryptocurrencies (
    creator_user_id, name, symbol, description, initial_price, current_price,
    total_supply, available_supply, blockchain_network, website, whitepaper,
    creator_fee_percentage, platform_fee_percentage, liquidity_pool_percentage,
    token_type, enable_burning, enable_minting, transferable, is_verified, is_active,
    contract_address, created_at, updated_at
) VALUES (
    @user_id, 'JustCoin', 'JCOIN', 'JustCoin is a utility token for the platform. It can be used to purchase premium content, subscribe to creators, and reward high-quality content.',
    0.01, 0.015, 1000000, 800000, 'binance', 'https://example.com/justcoin', 'https://example.com/justcoin/whitepaper',
    5.00, 2.50, 20.00, 'utility', 1, 0, 1, 1, 1,
    CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 40)), NOW(), NOW()
);

SET @justcoin_id = LAST_INSERT_ID();

-- ContentCreator Token
INSERT INTO cryptocurrencies (
    creator_user_id, name, symbol, description, initial_price, current_price,
    total_supply, available_supply, blockchain_network, website, whitepaper,
    creator_fee_percentage, platform_fee_percentage, liquidity_pool_percentage,
    token_type, enable_burning, enable_minting, transferable, is_verified, is_active,
    contract_address, created_at, updated_at
) VALUES (
    @user_id, 'ContentCreator Token', 'CCT', 'ContentCreator Token (CCT) is designed for content creators. Holders can participate in governance and earn revenue share from platform fees.',
    0.05, 0.08, 500000, 350000, 'ethereum', 'https://example.com/cct', 'https://example.com/cct/whitepaper',
    7.50, 2.00, 30.00, 'governance', 1, 1, 1, 1, 1,
    CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 40)), NOW(), NOW()
);

SET @cct_id = LAST_INSERT_ID();

-- FanCoin
INSERT INTO cryptocurrencies (
    creator_user_id, name, symbol, description, initial_price, current_price,
    total_supply, available_supply, blockchain_network, website, whitepaper,
    creator_fee_percentage, platform_fee_percentage, liquidity_pool_percentage,
    token_type, enable_burning, enable_minting, transferable, is_verified, is_active,
    contract_address, created_at, updated_at
) VALUES (
    @user_id, 'FanCoin', 'FAN', 'FanCoin is a social token that rewards fans for their engagement and loyalty. Use it to access exclusive content and experiences.',
    0.001, 0.0025, 10000000, 8000000, 'polygon', 'https://example.com/fancoin', 'https://example.com/fancoin/whitepaper',
    6.00, 1.50, 15.00, 'utility', 0, 1, 1, 0, 1,
    CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 40)), NOW(), NOW()
);

SET @fancoin_id = LAST_INSERT_ID();

-- Create creator wallets
-- JustCoin wallet
INSERT INTO crypto_wallets (
    user_id, cryptocurrency_id, balance, wallet_address, created_at, updated_at
) VALUES (
    @user_id, @justcoin_id, 100000, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 40)), NOW(), NOW()
);

-- CCT wallet
INSERT INTO crypto_wallets (
    user_id, cryptocurrency_id, balance, wallet_address, created_at, updated_at
) VALUES (
    @user_id, @cct_id, 50000, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 40)), NOW(), NOW()
);

-- FanCoin wallet
INSERT INTO crypto_wallets (
    user_id, cryptocurrency_id, balance, wallet_address, created_at, updated_at
) VALUES (
    @user_id, @fancoin_id, 1000000, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 40)), NOW(), NOW()
);

-- Create sample transactions
-- JustCoin transactions
INSERT INTO crypto_transactions (
    cryptocurrency_id, buyer_user_id, seller_user_id, type, amount, price_per_token,
    total_price, fee_amount, transaction_hash, status, created_at, updated_at
) VALUES
    (@justcoin_id, @user_id, NULL, 'buy', 500, 0.01, 5.00, 0.125, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY)),
    (@justcoin_id, @user_id, NULL, 'buy', 1000, 0.012, 12.00, 0.3, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY)),
    (@justcoin_id, @user_id, NULL, 'buy', 750, 0.014, 10.50, 0.2625, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY)),
    (@justcoin_id, NULL, @user_id, 'sell', 200, 0.015, 3.00, 0.075, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (@justcoin_id, @user_id, @user_id, 'transfer', 100, 0.015, 1.50, 0.0375, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (@justcoin_id, @user_id, NULL, 'mint', 1000, 0.01, 10.00, 0.25, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

-- CCT transactions
INSERT INTO crypto_transactions (
    cryptocurrency_id, buyer_user_id, seller_user_id, type, amount, price_per_token,
    total_price, fee_amount, transaction_hash, status, created_at, updated_at
) VALUES
    (@cct_id, @user_id, NULL, 'buy', 300, 0.05, 15.00, 0.3, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY)),
    (@cct_id, @user_id, NULL, 'buy', 500, 0.06, 30.00, 0.6, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY)),
    (@cct_id, NULL, @user_id, 'sell', 100, 0.07, 7.00, 0.14, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY)),
    (@cct_id, @user_id, NULL, 'reward', 50, 0.075, 3.75, 0.075, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (@cct_id, @user_id, NULL, 'buy', 200, 0.08, 16.00, 0.32, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

-- FanCoin transactions
INSERT INTO crypto_transactions (
    cryptocurrency_id, buyer_user_id, seller_user_id, type, amount, price_per_token,
    total_price, fee_amount, transaction_hash, status, created_at, updated_at
) VALUES
    (@fancoin_id, @user_id, NULL, 'buy', 5000, 0.001, 5.00, 0.075, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 30 DAY)),
    (@fancoin_id, @user_id, NULL, 'buy', 10000, 0.0015, 15.00, 0.225, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY)),
    (@fancoin_id, NULL, @user_id, 'sell', 2000, 0.002, 4.00, 0.06, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY)),
    (@fancoin_id, @user_id, NULL, 'mint', 20000, 0.002, 40.00, 0.6, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY)),
    (@fancoin_id, @user_id, NULL, 'buy', 8000, 0.0025, 20.00, 0.3, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (@fancoin_id, @user_id, @user_id, 'transfer', 1000, 0.0025, 2.50, 0.0375, CONCAT('0x', SUBSTRING(MD5(RAND()) FROM 1 FOR 64)), 'completed', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Success message
SELECT 'Sample cryptocurrency data inserted successfully!' AS Result; username