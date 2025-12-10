module.exports = (healthHandler) => {
    const express = require('express');
    const router = express.Router();
  
    router.get('/', healthHandler.getHealth);
  
    return router;
};