# NFT Marketplace Smart Contracts

This directory contains the Solidity smart contracts for the NFT Marketplace.

## Setup

1. Install dependencies:
```bash
npm install
```

2. Create a `.env` file with your network configuration:
```
PRIVATE_KEY=your_private_key_here
SEPOLIA_RPC_URL=https://sepolia.infura.io/v3/YOUR_INFURA_KEY
MUMBAI_RPC_URL=https://polygon-mumbai.infura.io/v3/YOUR_INFURA_KEY
```

## Compile Contracts

```bash
npm run compile
```

## Deploy Contracts

### Local Network (Hardhat)
```bash
npm run deploy:local
```

### Sepolia Testnet
```bash
npm run deploy:sepolia
```

### Mumbai Testnet (Polygon)
```bash
npm run deploy:mumbai
```

## Contract Functions

- `createToken(tokenURI, price)` - Create and list an NFT
- `createMarketSale(tokenId)` - Buy an NFT
- `reSellToken(tokenId, price)` - Resell an owned NFT
- `fetchMarketItem()` - Get all unsold NFTs
- `fetchMyNFT()` - Get NFTs owned by caller
- `fetchItemListed()` - Get NFTs listed by caller
- `getListingPrice()` - Get current listing price

