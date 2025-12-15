const asyncHandler = require('express-async-handler');
const ApiError = require('../../shared/errors/ApiError');
const IssDataValidator = require('../../application/validators/iss.validator');

class IssHandler {
  constructor(issService) {
    this.issService = issService;
    this.validator = new IssDataValidator();
  }

  getLatestPosition = asyncHandler(async (req, res) => {
    const validation = this.validator.validateLatestParams(req.query);
    
    if (!validation.isValid()) {
      throw new ApiError(400, 'Validation failed', {
        details: validation.getErrors(),
        code: 'VALIDATION_ERROR'
      });
    }
    
    const validatedParams = validation.getValidatedData();
    const position = await this.issService.getLatestPosition(validatedParams);
    
    if (!position) {
      throw new ApiError(404, 'No ISS position data available', {
        code: 'ISS_DATA_NOT_FOUND',
        timestamp: new Date().toISOString()
      });
    }
    
    const dataValidation = this.validator.validateIssApiData(
      position.payload || position
    );
    
    if (!dataValidation.isValid()) {
      console.warn('ISS data validation warnings:', dataValidation.getErrors());
    }
    
    res.json({
      success: true,
      data: {
        id: position.id,
        fetched_at: position.fetched_at,
        source_url: position.source_url,
        payload: position.payload,
        validated: dataValidation.isValid(),
        validation_warnings: dataValidation.isValid() ? [] : dataValidation.getErrors()
      },
      meta: {
        validated_params: validatedParams,
        timestamp: new Date().toISOString()
      }
    });
  });

  getTrend = asyncHandler(async (req, res) => {
    const validation = this.validator.validateTrendParams(req.query);
    
    if (!validation.isValid()) {
      throw new ApiError(400, 'Validation failed', {
        details: validation.getErrors(),
        code: 'VALIDATION_ERROR'
      });
    }
    
    const validatedParams = validation.getValidatedData();
    const trend = await this.issService.getTrend(validatedParams);
    
    res.json({
      success: true,
      data: trend,
      meta: {
        validated_params: validatedParams,
        deprecated_params_ignored: {
          hours: req.query.hours ? true : false,
          limit: req.query.limit ? true : false
        },
        timestamp: new Date().toISOString()
      }
    });
  });

  getHistory = asyncHandler(async (req, res) => {
    const validation = this.validator.validateHistoryParams(req.query);
    
    if (!validation.isValid()) {
      throw new ApiError(400, 'Validation failed', {
        details: validation.getErrors(),
        code: 'VALIDATION_ERROR'
      });
    }
    
    const validatedParams = validation.getValidatedData();
    const history = await this.issService.getHistory(validatedParams);
    
    const validatedItems = history.map(item => {
      const itemValidation = this.validator.validateIssApiData(
        item.payload || item
      );
      return {
        ...item,
        validated: itemValidation.isValid(),
        validation_warnings: itemValidation.isValid() ? [] : itemValidation.getErrors()
      };
    });
    
    res.json({
      success: true,
      data: validatedItems,
      meta: {
        total: validatedItems.length,
        validated_count: validatedItems.filter(item => item.validated).length,
        validated_params: validatedParams,
        timestamp: new Date().toISOString()
      }
    });
  });

  triggerFetch = asyncHandler(async (req, res) => {
    if (req.body && Object.keys(req.body).length > 0) {
      const validation = this.validator.validateIssApiData(req.body);
      if (!validation.isValid()) {
        throw new ApiError(400, 'Invalid input data for fetch', {
          details: validation.getErrors(),
          code: 'VALIDATION_ERROR'
        });
      }
    }
    
    const position = await this.issService.fetchAndStorePosition();
    
    if (!position) {
      throw new ApiError(500, 'Failed to fetch and store ISS position', {
        code: 'FETCH_FAILED',
        timestamp: new Date().toISOString()
      });
    }
    
    const dataValidation = this.validator.validateIssApiData(
      position.payload || position
    );
    
    res.json({
      success: true,
      data: {
        id: position.id,
        fetched_at: position.fetched_at,
        source_url: position.source_url,
        payload: position.payload,
        validated: dataValidation.isValid(),
        validation_errors: dataValidation.isValid() ? [] : dataValidation.getErrors()
      },
      meta: {
        operation: 'manual_fetch',
        timestamp: new Date().toISOString()
      }
    });
  });

  calculateDistance = asyncHandler(async (req, res) => {
    const validation = this.validator.validateHaversineParams(req.query);
    
    if (!validation.isValid()) {
      throw new ApiError(400, 'Invalid coordinates for distance calculation', {
        details: validation.getErrors(),
        code: 'VALIDATION_ERROR'
      });
    }
    
    const { lat1, lon1, lat2, lon2 } = validation.getValidatedData();
    
    const distance = this.calculateHaversine(lat1, lon1, lat2, lon2);
    
    res.json({
      success: true,
      data: {
        distance_km: distance,
        distance_miles: distance * 0.621371,
        distance_nautical: distance * 0.539957,
        point1: { lat: lat1, lon: lon1 },
        point2: { lat: lat2, lon: lon2 }
      },
      meta: {
        calculation: 'haversine',
        validated_params: validation.getValidatedData(),
        timestamp: new Date().toISOString()
      }
    });
  });

  validateData = asyncHandler(async (req, res) => {
    const validation = this.validator.validateIssApiData(req.body);
    
    res.json({
      success: validation.isValid(),
      data: validation.getValidatedData(),
      errors: validation.getErrors(),
      meta: {
        operation: 'validation_test',
        timestamp: new Date().toISOString()
      }
    });
  });

  calculateHaversine(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = this.toRad(lat2 - lat1);
    const dLon = this.toRad(lon2 - lon1);
    
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(this.toRad(lat1)) * Math.cos(this.toRad(lat2)) * 
              Math.sin(dLon/2) * Math.sin(dLon/2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
  }

  toRad(degrees) {
    return degrees * (Math.PI / 180);
  }
}

module.exports = IssHandler;