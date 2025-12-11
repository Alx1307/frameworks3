module.exports = (astronomyHandler) => {
    const express = require('express');
    const router = express.Router();
  
    router.get('/events', astronomyHandler.getEvents);
    router.get('/config', astronomyHandler.getConfig);
    router.get('/test', astronomyHandler.testApi);
  
    return router;
  };