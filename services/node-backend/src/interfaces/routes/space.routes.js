module.exports = (spaceHandler) => {
  const express = require('express');
  const router = express.Router();

  router.get('/:src/latest', spaceHandler.getLatest);
  router.get('/refresh', spaceHandler.refresh);
  router.get('/summary', spaceHandler.getSummary);

  return router;
};