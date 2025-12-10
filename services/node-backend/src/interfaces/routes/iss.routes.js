module.exports = (issHandler) => {
  const express = require('express');
  const router = express.Router();

  router.get('/latest', issHandler.getLatestPosition);
  router.get('/trend', issHandler.getTrend);
  router.get('/fetch', issHandler.triggerFetch);

  return router;
};