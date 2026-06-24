/**
 * Web3 Integration for NFT Marketplace
 * Handles MetaMask connections and blockchain interactions
 */

class Web3Integration {
    constructor() {
        this.contractAddress = null;
        this.contractABI = null;
        this.web3 = null;
        this.contract = null;
        this.account = null;
    }

    /**
     * Initialize Web3 and load contract
     */
    async init() {
        if (typeof window.ethereum === 'undefined') {
            console.error('MetaMask is not installed');
            return false;
        }

        try {
            // Request account access
            const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
            this.account = accounts[0];

            // Load contract ABI and address
            const response = await fetch('/api/nft/contract-abi');
            const data = await response.json();
            
            this.contractAddress = data.contract_address;
            this.contractABI = data.abi;

            // Setup Web3 (using ethers.js would be better, but this is a basic implementation)
            this.web3 = new Web3(window.ethereum);

            // Create contract instance
            if (this.contractABI && this.contractAddress) {
                this.contract = new this.web3.eth.Contract(this.contractABI, this.contractAddress);
            }

            // Listen for account changes
            window.ethereum.on('accountsChanged', (accounts) => {
                this.account = accounts[0];
                window.location.reload();
            });

            // Listen for chain changes
            window.ethereum.on('chainChanged', (chainId) => {
                window.location.reload();
            });

            return true;
        } catch (error) {
            console.error('Error initializing Web3:', error);
            return false;
        }
    }

    /**
     * Get current account
     */
    getAccount() {
        return this.account;
    }

    /**
     * Get listing price from contract
     */
    async getListingPrice() {
        if (!this.contract) {
            const response = await fetch('/api/nft/listing-price');
            const data = await response.json();
            return parseFloat(data.listing_price);
        }

        try {
            const price = await this.contract.methods.getListingPrice().call();
            return this.web3.utils.fromWei(price, 'ether');
        } catch (error) {
            console.error('Error getting listing price:', error);
            return 0.0025; // Default
        }
    }

    /**
     * Create NFT token
     */
    async createToken(tokenURI, price) {
        if (!this.contract || !this.account) {
            throw new Error('Web3 not initialized or account not connected');
        }

        try {
            const listingPrice = await this.contract.methods.getListingPrice().call();
            const totalPrice = this.web3.utils.toBN(
                this.web3.utils.toWei(price.toString(), 'ether')
            ).add(this.web3.utils.toBN(listingPrice));

            const tx = await this.contract.methods.createToken(
                tokenURI,
                this.web3.utils.toWei(price.toString(), 'ether')
            ).send({
                from: this.account,
                value: totalPrice.toString(),
                gas: 500000
            });

            return {
                success: true,
                transactionHash: tx.transactionHash,
                tokenId: tx.events?.idMarketItemCreated?.returnValues?.tokenId
            };
        } catch (error) {
            console.error('Error creating token:', error);
            throw error;
        }
    }

    /**
     * Buy NFT
     */
    async buyNFT(tokenId, price) {
        if (!this.contract || !this.account) {
            throw new Error('Web3 not initialized or account not connected');
        }

        try {
            const tx = await this.contract.methods.createMarketSale(tokenId).send({
                from: this.account,
                value: this.web3.utils.toWei(price.toString(), 'ether'),
                gas: 500000
            });

            return {
                success: true,
                transactionHash: tx.transactionHash
            };
        } catch (error) {
            console.error('Error buying NFT:', error);
            throw error;
        }
    }

    /**
     * Resell NFT
     */
    async resellToken(tokenId, price) {
        if (!this.contract || !this.account) {
            throw new Error('Web3 not initialized or account not connected');
        }

        try {
            const listingPrice = await this.contract.methods.getListingPrice().call();
            const totalPrice = this.web3.utils.toBN(
                this.web3.utils.toWei(price.toString(), 'ether')
            ).add(this.web3.utils.toBN(listingPrice));

            const tx = await this.contract.methods.reSellToken(
                tokenId,
                this.web3.utils.toWei(price.toString(), 'ether')
            ).send({
                from: this.account,
                value: listingPrice,
                gas: 500000
            });

            return {
                success: true,
                transactionHash: tx.transactionHash
            };
        } catch (error) {
            console.error('Error reselling NFT:', error);
            throw error;
        }
    }

    /**
     * Fetch market items
     */
    async fetchMarketItems() {
        if (!this.contract) {
            return [];
        }

        try {
            const items = await this.contract.methods.fetchMarketItem().call();
            return items;
        } catch (error) {
            console.error('Error fetching market items:', error);
            return [];
        }
    }

    /**
     * Fetch user's NFTs
     */
    async fetchMyNFTs() {
        if (!this.contract || !this.account) {
            return [];
        }

        try {
            const items = await this.contract.methods.fetchMyNFT().call();
            return items;
        } catch (error) {
            console.error('Error fetching my NFTs:', error);
            return [];
        }
    }

    /**
     * Format ETH value
     */
    formatETH(wei) {
        if (!this.web3) return wei;
        return parseFloat(this.web3.utils.fromWei(wei, 'ether'));
    }
}

// Global instance
window.web3Integration = new Web3Integration();

// Auto-initialize on page load
if (typeof window.ethereum !== 'undefined') {
    window.web3Integration.init().then(success => {
        if (success) {
            console.log('Web3 initialized successfully');
        }
    });
}

