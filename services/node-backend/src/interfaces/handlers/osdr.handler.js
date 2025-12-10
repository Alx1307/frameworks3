const asyncHandler = require('express-async-handler');
const ApiError = require('../../shared/errors/ApiError');

class OsdrHandler {
  constructor(osdrService) {
    this.osdrService = osdrService;
  }

  syncOsdr = asyncHandler(async (req, res) => {
    const result = await this.osdrService.fetchAndStoreOsdr();
    
    res.json({ written: result.written });
  });

  listOsdr = asyncHandler(async (req, res) => {
    const limit = req.query.limit ? parseInt(req.query.limit) : 20;
    
    if (isNaN(limit) || limit < 1 || limit > 100) {
      throw new ApiError(400, 'Limit must be between 1 and 100');
    }
    
    const result = await this.osdrService.getOsdrList(limit);
    
    res.json({ items: result.items });
  });
}

module.exports = OsdrHandler;