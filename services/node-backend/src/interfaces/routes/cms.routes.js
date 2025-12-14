const express = require('express');
const { body, param, query } = require('express-validator');
const { validateRequest } = require('../../application/middlewares/validation.middleware');

function createCmsRouter(cmsHandler) {
  const router = express.Router();

  router.get('/blocks', cmsHandler.getAllBlocks.bind(cmsHandler));

  router.get(
    '/search',
    [
      query('q').trim().notEmpty().withMessage('Search query is required')
    ],
    validateRequest,
    cmsHandler.searchBlocks.bind(cmsHandler)
  );

  router.get(
    '/block/:slug',
    [
      param('slug').trim().notEmpty().matches(/^[a-z0-9-_]+$/).withMessage('Invalid slug format')
    ],
    validateRequest,
    cmsHandler.getBlockBySlug.bind(cmsHandler)
  );

  router.get(
    '/block-by-id/:id',
    [
      param('id').isInt().withMessage('Invalid block ID')
    ],
    validateRequest,
    cmsHandler.getBlockById.bind(cmsHandler)
  );

  router.post(
    '/block',
    [
      body('slug').trim().notEmpty().matches(/^[a-z0-9-_]+$/).withMessage('Invalid slug format'),
      body('title').trim().notEmpty().isLength({ max: 255 }).withMessage('Title is required'),
      body('content').trim().notEmpty().withMessage('Content is required'),
      body('is_active').optional().isBoolean()
    ],
    validateRequest,
    cmsHandler.createBlock.bind(cmsHandler)
  );

  router.put(
    '/block/:id',
    [
      param('id').isInt().withMessage('Invalid block ID'),
      body('slug').optional().trim().matches(/^[a-z0-9-_]+$/).withMessage('Invalid slug format'),
      body('title').optional().trim().isLength({ max: 255 }),
      body('content').optional().trim(),
      body('is_active').optional().isBoolean()
    ],
    validateRequest,
    cmsHandler.updateBlock.bind(cmsHandler)
  );

  router.delete(
    '/block/:id',
    [
      param('id').isInt().withMessage('Invalid block ID')
    ],
    validateRequest,
    cmsHandler.deleteBlock.bind(cmsHandler)
  );

  return router;
}

module.exports = createCmsRouter;