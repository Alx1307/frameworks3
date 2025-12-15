const express = require('express');
const router = express.Router();

module.exports = (handler) => {
  if (!handler) {
    throw new Error('JwstHandler is required for jwst.routes');
  }

  const requiredMethods = ['getFeed', 'getInstruments', 'getHealth', 'refreshCache', 'getStatus'];
  for (const method of requiredMethods) {
    if (typeof handler[method] !== 'function') {
      console.error(`Warning: JwstHandler is missing method: ${method}`);
      handler[method] = (req, res) => res.status(501).json({ 
        error: `Method ${method} not implemented` 
      });
    }
  }

  router.get('/', (req, res) => {
    if (handler.getStatus) {
      return handler.getStatus(req, res);
    }
    res.json({ service: 'jwst', status: 'online', timestamp: new Date().toISOString() });
  });

  router.get('/feed', handler.getFeed.bind(handler));
  router.get('/instruments', handler.getInstruments.bind(handler));
  router.get('/health', handler.getHealth.bind(handler));
  router.post('/refresh', handler.refreshCache.bind(handler));
  
  router.get('/programs', (req, res) => {
    res.status(501).json({ 
      success: false, 
      message: 'This endpoint is deprecated. Use /feed with program filter instead.',
      timestamp: new Date().toISOString()
    });
  });
  
  router.get('/image/:id', (req, res) => {
    res.status(501).json({ 
      success: false, 
      message: 'This endpoint is not implemented yet.',
      timestamp: new Date().toISOString()
    });
  });

  return router;
};