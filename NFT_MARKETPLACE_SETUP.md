# NFT Marketplace Implementation Guide

This document provides a complete guide to implementing the NFT Marketplace using the Solidity smart contract.

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Smart Contract Setup](#smart-contract-setup)
3. [Laravel Backend Setup](#laravel-backend-setup)
4. [Frontend Integration](#frontend-integration)
5. [Deployment](#deployment)
6. [Testing](#testing)

## Prerequisites

- Node.js (v16+)
- PHP 8.0+
- Laravel 9+
- MetaMask browser extension
- Hardhat (for smart contract development)

## Smart Contract Setup

### 1. Install Dependencies

```bash
cd contracts
npm install
```

### 2. Configure Environment

Create a `.env` file in the `contracts` directory:

```env
PRIVATE_KEY=your_private_key_here
SEPOLIA_RPC_URL=https://sepolia.infura.io/v3/YOUR_INFURA_KEY
MUMBAI_RPC_URL=https://polygon-mumbai.infura.io/v3/YOUR_INFURA_KEY
```

### 3. Compile Contracts

```bash
npm run compile
```

### 4. Deploy Contracts

#### Local Network (Hardhat)
```bash
npm run deploy:local
```

#### Sepolia Testnet
```bash
npm run deploy:sepolia
```

#### Mumbai Testnet (Polygon)
```bash
npm run deploy:mumbai
```

After deployment, the contract address will be saved to `.env.contract` in the project root.

## Laravel Backend Setup

### 1. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `nfts` - NFT records
- `nft_listings` - Active listings
- `nft_transactions` - Transaction history

### 2. Configure Environment

Add to your `.env` file:

```env
WEB3_RPC_URL=http://127.0.0.1:8545
NFT_MARKETPLACE_CONTRACT_ADDRESS=0x...
WEB3_NETWORK=localhost
WEB3_CHAIN_ID=1337
NFT_LISTING_PRICE=0.0025
```

### 3. Update Contract Address

After deploying the contract, update the contract address in `.env`:

```env
NFT_MARKETPLACE_CONTRACT_ADDRESS=0xYourDeployedContractAddress
```

## Frontend Integration

### 1. Install Web3.js

The frontend uses Web3.js for blockchain interactions. You can include it via CDN or install via npm:

```html
<script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
```

Or add to your `package.json`:

```json
{
  "dependencies": {
    "web3": "^4.0.0"
  }
}
```

### 2. MetaMask Integration

The `public/js/web3.js` file provides a Web3Integration class that handles:
- MetaMask connection
- Contract interactions
- Transaction management

### 3. Available Routes

- `/nft/marketplace` - Browse all NFTs
- `/nft/create` - Create a new NFT
- `/nft/{id}` - View NFT details
- `/nft/my/nfts` - View your NFTs
- `/nft/my/listings` - View your listings
- `/nft/resell/{id}` - Resell an NFT

### 4. API Endpoints

- `GET /nft/api/contract-abi` - Get contract ABI
- `GET /nft/api/listing-price` - Get current listing price

## Deployment

### 1. Deploy Smart Contract

Choose your network and deploy:

```bash
cd contracts
npm run deploy:sepolia  # or deploy:mumbai for Polygon
```

### 2. Update Laravel Config

Update `.env` with the deployed contract address and network settings.

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Testing

### 1. Local Testing

1. Start local Hardhat node:
```bash
cd contracts
npx hardhat node
```

2. Deploy to local network:
```bash
npm run deploy:local
```

3. Configure MetaMask:
   - Network: Localhost 8545
   - Chain ID: 1337
   - Import test accounts from Hardhat

### 2. Testnet Testing

1. Get testnet ETH from faucets:
   - Sepolia: https://sepoliafaucet.com/
   - Mumbai: https://faucet.polygon.technology/

2. Deploy contract to testnet
3. Test all functionality

## Smart Contract Functions

### Public Functions

- `createToken(tokenURI, price)` - Create and list an NFT
- `createMarketSale(tokenId)` - Buy an NFT
- `reSellToken(tokenId, price)` - Resell an owned NFT
- `fetchMarketItem()` - Get all unsold NFTs
- `fetchMyNFT()` - Get NFTs owned by caller
- `fetchItemListed()` - Get NFTs listed by caller
- `getListingPrice()` - Get current listing price

### Owner Functions

- `updateListingPrice(_listingPrice)` - Update listing price (owner only)

## Important Notes

1. **Gas Fees**: All transactions require ETH for gas fees
2. **Listing Fee**: Sellers must pay a listing fee (default: 0.0025 ETH)
3. **Transaction Confirmation**: Wait for blockchain confirmation before updating database
4. **IPFS**: For production, upload NFT metadata to IPFS instead of local storage
5. **Security**: Never expose private keys in frontend code

## Troubleshooting

### MetaMask Not Connecting
- Ensure MetaMask is installed and unlocked
- Check network configuration matches your deployment
- Verify contract address is correct

### Transactions Failing
- Check you have enough ETH for gas
- Verify contract address is correct
- Check network matches (localhost, sepolia, mumbai)

### Contract ABI Not Loading
- Ensure contract is compiled: `npm run compile`
- Check artifacts directory exists
- Verify file permissions

## Next Steps

1. **IPFS Integration**: Upload NFT images and metadata to IPFS
2. **Advanced Features**: Add auctions, royalties, collections
3. **Analytics**: Track marketplace statistics
4. **Notifications**: Notify users of sales and listings
5. **Search & Filters**: Enhanced marketplace browsing

## Support

For issues or questions, refer to:
- Hardhat Documentation: https://hardhat.org/docs
- OpenZeppelin Contracts: https://docs.openzeppelin.com/contracts
- Web3.js Documentation: https://web3js.readthedocs.io

