const express = require('express');
const router = express.Router();

module.exports = (issHandler) => {
  if (!issHandler) {
    throw new Error('IssHandler is required for iss.routes');
  }

  const requiredMethods = ['getLatestPosition', 'getTrend', 'triggerFetch', 'getHistory'];
  for (const method of requiredMethods) {
    if (typeof issHandler[method] !== 'function') {
      throw new Error(`IssHandler is missing required method: ${method}`);
    }
  }

  const logRequest = (req, res, next) => {
    console.log(`[${new Date().toISOString()}] ${req.method} ${req.path}`);
    next();
  };

  const validateQueryParams = (req, res, next) => {
    const validationErrors = [];
    
    if (req.query.limit !== undefined) {
      const limit = parseInt(req.query.limit);
      if (isNaN(limit) || limit < 1 || limit > 1000) {
        validationErrors.push({
          field: 'limit',
          message: 'limit must be a number between 1 and 1000'
        });
      }
      req.validatedLimit = limit;
    }
    
    if (req.query.hours !== undefined) {
      const hours = parseInt(req.query.hours);
      if (isNaN(hours) || hours < 1 || hours > 720) {
        validationErrors.push({
          field: 'hours',
          message: 'hours must be a number between 1 and 720'
        });
      }
      req.validatedHours = hours;
    }
    
    if (req.path.includes('/history')) {
      if (req.query.start_date !== undefined) {
        const date = new Date(req.query.start_date);
        if (isNaN(date.getTime())) {
          validationErrors.push({
            field: 'start_date',
            message: 'start_date must be a valid ISO date string'
          });
        }
      }
      
      if (req.query.end_date !== undefined) {
        const date = new Date(req.query.end_date);
        if (isNaN(date.getTime())) {
          validationErrors.push({
            field: 'end_date',
            message: 'end_date must be a valid ISO date string'
          });
        }
      }
    }
    
    if (validationErrors.length > 0) {
      return res.status(400).json({
        success: false,
        error: {
          message: 'Query parameter validation failed',
          code: 'VALIDATION_ERROR',
          details: validationErrors,
          timestamp: new Date().toISOString()
        }
      });
    }
    
    next();
  };

  router.get('/', logRequest, (req, res) => {
    res.json({
      service: 'iss-api',
      version: '1.0.0',
      endpoints: [
        { path: '/api/iss/latest', method: 'GET', description: 'Latest ISS position' },
        { path: '/api/iss/trend', method: 'GET', description: 'ISS position trends' },
        { path: '/api/iss/history', method: 'GET', description: 'Historical ISS data' },
        { path: '/api/iss/fetch', method: 'GET', description: 'Trigger manual ISS data fetch' }
      ],
      timestamp: new Date().toISOString()
    });
  });

  router.get('/latest', 
    logRequest,
    validateQueryParams,
    issHandler.getLatestPosition
  );

  router.get('/trend', 
    logRequest,
    validateQueryParams,
    issHandler.getTrend
  );

  router.get('/fetch', 
    logRequest,
    issHandler.triggerFetch
  );

  router.get('/history', 
    logRequest,
    validateQueryParams,
    issHandler.getHistory
  );

  router.post('/validate', 
    logRequest,
    (req, res) => {
      try {
        const validationErrors = [];
        
        if (req.body && req.body.latitude !== undefined) {
          const lat = parseFloat(req.body.latitude);
          if (isNaN(lat) || lat < -90 || lat > 90) {
            validationErrors.push({
              field: 'latitude',
              message: 'latitude must be a number between -90 and 90'
            });
          }
        }
        
        if (req.body && req.body.longitude !== undefined) {
          const lon = parseFloat(req.body.longitude);
          if (isNaN(lon) || lon < -180 || lon > 180) {
            validationErrors.push({
              field: 'longitude',
              message: 'longitude must be a number between -180 and 180'
            });
          }
        }
        
        if (req.body && req.body.timestamp !== undefined) {
          const date = new Date(req.body.timestamp);
          if (isNaN(date.getTime())) {
            validationErrors.push({
              field: 'timestamp',
              message: 'timestamp must be a valid ISO date string'
            });
          }
        }
        
        if (validationErrors.length > 0) {
          return res.status(400).json({
            success: false,
            error: {
              message: 'Request body validation failed',
              code: 'VALIDATION_ERROR',
              details: validationErrors,
              timestamp: new Date().toISOString()
            }
          });
        }
        
        res.json({
          success: true,
          message: 'Data is valid',
          validated_data: req.body,
          timestamp: new Date().toISOString()
        });
        
      } catch (error) {
        console.error('Validation error:', error);
        res.status(500).json({
          success: false,
          error: 'Internal validation error'
        });
      }
    }
  );

  return router;
};