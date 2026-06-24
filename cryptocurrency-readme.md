# Cryptocurrency Sample Data

This documentation explains how to insert sample cryptocurrency data into your application for testing and development purposes. We have provided multiple ways to achieve this, so you can choose the method that works best for your workflow.

## Available Methods

### 1. Web Route (Easiest)

Simply visit the following URL in your browser after logging in:

```
http://your-domain.com/seed-cryptocurrency
```

This will insert the sample data and show you a confirmation page with details of what was created.

### 2. Artisan Command (Terminal)

Run the following command from your terminal in the project root directory:

```bash
php artisan seed:cryptocurrency
```

This will insert the sample data and show progress in the terminal.

### 3. PHP Script

Execute the following PHP script:

```bash
php crypto_sample_data.php
```

### 4. SQL Script

You can also directly execute the SQL script in your database management tool:

```bash
mysql -u your_username -p your_database < crypto_sample_sql.sql
```

Or copy and paste the contents of `crypto_sample_sql.sql` into your database management tool.

## Sample Data Overview

The sample data includes three cryptocurrencies:

1. **JustCoin (JCOIN)**
   - Utility token for the platform
   - Initial price: $0.01
   - Current price: $0.015
   - Total supply: 1,000,000

2. **ContentCreator Token (CCT)**
   - Governance token for content creators
   - Initial price: $0.05
   - Current price: $0.08
   - Total supply: 500,000

3. **FanCoin (FAN)**
   - Social token for fans and community
   - Initial price: $0.001
   - Current price: $0.0025
   - Total supply: 10,000,000

For each cryptocurrency, the following will be created:
- A wallet for the first user in the system
- 5-10 random transactions of various types (buy, sell, transfer, etc.)
- Contract addresses and other metadata

## After Installation

Once the data is inserted, you can visit these URLs to see your cryptocurrencies:
- http://your-domain.com/cryptocurrency/1
- http://your-domain.com/cryptocurrency/2
- http://your-domain.com/cryptocurrency/3

You can also check your wallet at:
- http://your-domain.com/cryptocurrency/wallet

## Troubleshooting

If you encounter any issues:

- Make sure you have at least one user in your system (the script uses the first user as the creator)
- Check that your database tables are properly created (`cryptocurrencies`, `crypto_wallets`, `crypto_transactions`)
- Ensure you have the correct namespace for your models (the scripts attempt to detect whether to use `App\Models` or `App\Model`)

If you need to start fresh, you can truncate the crypto-related tables:

```sql
TRUNCATE TABLE crypto_transactions;
TRUNCATE TABLE crypto_wallets;
TRUNCATE TABLE cryptocurrencies;
``` 