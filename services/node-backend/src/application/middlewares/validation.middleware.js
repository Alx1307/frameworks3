const IssDataValidator = require('../validators/iss.validator');

function validateRequest(schema, validatorType = 'body') {
  return async (req, res, next) => {
    try {
      let validator;
      const data = validatorType === 'query' ? req.query : req.body;
      
      switch (schema) {
        case 'iss_position':
          validator = new IssDataValidator();
          validator.validateIssPosition(data);
          break;
          
        case 'iss_query':
          validator = new IssDataValidator();
          validator.validateIssQueryParams(data);
          break;
          
        case 'jwst_filters':
          validator = new JwstDataValidator();
          validator.validateImageFilters(data);
          break;
          
        case 'osdr_dataset':
          validator = new OsdrDataValidator();
          validator.validateDatasetFilters(data);
          break;
          
        default:
          return next();
      }
      
      if (!validator.isValid()) {
        return res.status(400).json({
          success: false,
          error: {
            message: 'Validation failed',
            code: 'VALIDATION_ERROR',
            details: validator.getErrors(),
            timestamp: new Date().toISOString()
          }
        });
      }
      
      req.validatedData = validator.getValidatedData();
      next();
      
    } catch (error) {
      console.error('Validation middleware error:', error);
      return res.status(500).json({
        success: false,
        error: 'Internal validation error'
      });
    }
  };
}

module.exports = { validateRequest };