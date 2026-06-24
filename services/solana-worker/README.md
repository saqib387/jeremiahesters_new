# Solana worker (stub)

HTTP sidecar expected by Laravel (`config/solana.php`). This build returns **stub** mint addresses so Phase 1 queue + DB flows can be tested without Helius or on-chain programs.

## Run locally

```bash
cd services/solana-worker
npm install
PORT=8787 node src/index.js
```

Optional shared secret (must match `SOLANA_WORKER_SECRET` in Laravel `.env`):

```bash
WORKER_SECRET=devsecret PORT=8787 node src/index.js
```

## Replace with production worker

Swap `src/index.js` for Metaplex + `@solana/web3.js` minting, Jupiter swaps, and gRPC if you adopt the spec’s Laravel ↔ Node interface.
