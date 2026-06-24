const hre = require("hardhat");

async function main() {
  console.log("Deploying NFT Marketplace Contract...");

  const NFTMarketplace = await hre.ethers.getContractFactory("NFTMarketplace");
  const nftMarketplace = await NFTMarketplace.deploy();

  await nftMarketplace.waitForDeployment();

  const contractAddress = await nftMarketplace.getAddress();
  console.log("NFT Marketplace deployed to:", contractAddress);

  // Save the contract address to a file for Laravel to use
  const fs = require("fs");
  const contractInfo = {
    address: contractAddress,
    network: hre.network.name,
    deployedAt: new Date().toISOString()
  };

  fs.writeFileSync(
    "../.env.contract",
    `NFT_MARKETPLACE_CONTRACT_ADDRESS=${contractAddress}\nNFT_MARKETPLACE_NETWORK=${hre.network.name}\n`
  );

  console.log("Contract info saved to .env.contract");
}

main()
  .then(() => process.exit(0))
  .catch((error) => {
    console.error(error);
    process.exit(1);
  });

