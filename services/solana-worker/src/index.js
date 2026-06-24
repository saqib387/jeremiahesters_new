'use strict';

const express = require('express');

const PORT = parseInt(process.env.PORT || '8787', 10);
const WORKER_SECRET = process.env.WORKER_SECRET || '';

const app = express();
app.use(express.json({ limit: '256kb' }));

function authOk(req) {
  if (!WORKER_SECRET) {
    return true;
  }
  return req.get('X-Worker-Secret') === WORKER_SECRET;
}

app.get('/health', (req, res) => {
  if (!authOk(req)) {
    return res.status(401).json({ ok: false, error: 'unauthorized' });
  }
  res.json({
    ok: true,
    service: 'platform-solana-worker',
    mode: 'stub',
    port: PORT,
  });
});

/**
 * POST /v1/mint
 * Body: { userId, name?, username?, symbol? }
 * Returns deterministic stub mint address until real Solana mint is wired.
 */
app.post('/v1/mint', (req, res) => {
  if (!authOk(req)) {
    return res.status(401).json({ ok: false, error: 'unauthorized' });
  }

  const userId = req.body && req.body.userId;
  if (userId === undefined || userId === null) {
    return res.status(400).json({ ok: false, error: 'userId required' });
  }

  const mintAddress = `STUB_MINT_${String(userId)}_${Date.now()}`;
  res.json({
    ok: true,
    mintAddress,
    mode: 'stub',
    symbol: req.body.symbol || null,
    name: req.body.name || null,
  });
});

app.listen(PORT, '0.0.0.0', () => {
  // eslint-disable-next-line no-console
  console.log(`solana-worker listening on ${PORT} (stub mode)`);
});
