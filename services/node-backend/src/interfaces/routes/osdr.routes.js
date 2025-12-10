module.exports = (osdrHandler) => {
  const express = require('express');
  const router = express.Router();

  router.get('/sync', osdrHandler.syncOsdr);
  router.get('/list', osdrHandler.listOsdr);

  return router;
};