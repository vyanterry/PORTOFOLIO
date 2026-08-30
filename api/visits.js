import { kv } from '@vercel/kv';

export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  if (req.method !== 'GET') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    const count = await kv.incr('portfolio:visits');
    console.log('[Visits API] Incremented to:', count);
    
    return res.json({
      count,
      success: true,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    console.error('[Visits API] Error:', error.message);
    return res.status(500).json({
      error: error.message,
      success: false
    });
  }
}
