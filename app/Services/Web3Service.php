<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * @deprecated Replaced by App\Services\Nft\* (NftMintingService / MetadataStorageService).
 *
 * This class previously returned FAKE transaction hashes (random bytes) for mint/sale/resell,
 * which silently faked on-chain activity. Those write methods now throw so nothing can mistake
 * a stub for a real transaction. Read helpers (RPC receipt lookup, unit conversion) remain.
 */
class Web3Service
{
    protected $rpcUrl;
    protected $contractAddress;
    protected $contractAbi;

    public function __construct()
    {
        $this->rpcUrl = config('web3.rpc_url', 'http://127.0.0.1:8545');
        $this->contractAddress = config('web3.contract_address');
        $this->contractAbi = $this->loadContractAbi();
    }

    /**
     * Load contract ABI from artifacts
     */
    protected function loadContractAbi()
    {
        $abiPath = base_path('contracts/artifacts/contracts/NFTMarketplace.sol/NFTMarketplace.json');
        
        if (file_exists($abiPath)) {
            $artifact = json_decode(file_get_contents($abiPath), true);
            return $artifact['abi'] ?? [];
        }

        return [];
    }

    /**
     * Make JSON-RPC call to blockchain
     */
    protected function rpcCall(string $method, array $params = [])
    {
        try {
            $response = Http::post($this->rpcUrl, [
                'jsonrpc' => '2.0',
                'method' => $method,
                'params' => $params,
                'id' => 1,
            ]);

            $data = $response->json();
            
            if (isset($data['error'])) {
                Log::error('Web3 RPC Error', [
                    'method' => $method,
                    'error' => $data['error'],
                ]);
                return null;
            }

            return $data['result'] ?? null;
        } catch (\Exception $e) {
            Log::error('Web3 RPC Exception', [
                'method' => $method,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Encode function call
     */
    protected function encodeFunctionCall(string $functionName, array $params)
    {
        // This is a simplified version. In production, use a proper ABI encoder
        // For now, we'll return the function signature
        // You may want to use a library like kornrunner/keccak for proper encoding
        return $functionName;
    }

    /**
     * Get listing price from contract
     */
    public function getListingPrice()
    {
        // This would call the contract's getListingPrice() function
        // Implementation depends on your RPC setup
        return 0.0025; // Default value in ETH
    }

    /**
     * Create NFT token on blockchain
     */
    public function createToken(string $tokenURI, float $price, string $fromAddress, string $privateKey = null)
    {
        // This would interact with the contract's createToken function
        // In a real implementation, you'd need to:
        // 1. Encode the function call
        // 2. Sign the transaction
        // 3. Send it to the blockchain
        // 4. Wait for confirmation
        
        throw new RuntimeException('Web3Service::createToken is deprecated. Mint via App\\Services\\Nft\\Contracts\\NftMintingService (thirdweb) instead.');
    }

    /**
     * Buy NFT from marketplace
     */
    public function createMarketSale(int $tokenId, float $price, string $fromAddress, string $privateKey = null)
    {
        throw new RuntimeException('Web3Service::createMarketSale is deprecated. Real on-chain sales are handled in the marketplace phase.');
    }

    /**
     * Resell NFT
     */
    public function resellToken(int $tokenId, float $price, string $fromAddress, string $privateKey = null)
    {
        throw new RuntimeException('Web3Service::resellToken is deprecated. Real on-chain resale is handled in the marketplace phase.');
    }

    /**
     * Fetch market items from contract
     */
    public function fetchMarketItems()
    {
        // This would call the contract's fetchMarketItem() function
        // Return array of MarketItem structs
        return [];
    }

    /**
     * Fetch user's NFTs
     */
    public function fetchMyNFTs(string $userAddress)
    {
        // This would call the contract's fetchMyNFT() function
        return [];
    }

    /**
     * Fetch user's listed items
     */
    public function fetchItemListed(string $userAddress)
    {
        // This would call the contract's fetchItemListed() function
        return [];
    }

    /**
     * Get transaction receipt
     */
    public function getTransactionReceipt(string $transactionHash)
    {
        return $this->rpcCall('eth_getTransactionReceipt', [$transactionHash]);
    }

    /**
     * Convert ETH to Wei
     */
    public function ethToWei(float $eth)
    {
        return (string)($eth * 1e18);
    }

    /**
     * Convert Wei to ETH
     */
    public function weiToEth(string $wei)
    {
        return (float)($wei / 1e18);
    }
}

