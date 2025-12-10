const asyncHandler = require('express-async-handler');
const ApiError = require('../../shared/errors/ApiError');

class IssHandler {
  constructor(issService) {
    this.issService = issService;
  }

  getLatestPosition = asyncHandler(async (req, res) => {
    const position = await this.issService.getLatestPosition();
    
    if (!position) {
      throw new ApiError(404, 'No ISS position data available');
    }
    
    res.json({
      id: position.id,
      fetched_at: position.fetched_at,
      source_url: position.source_url,
      payload: position.payload
    });
  });

  getTrend = asyncHandler(async (req, res) => {
    const { hours, limit } = req.query;
    
    if (hours || limit) {
      console.log(`Ignoring deprecated parameters: hours=${hours}, limit=${limit}`);
    }
    
    const trend = await this.issService.getTrend();
    
    res.json(trend);
  });

  triggerFetch = asyncHandler(async (req, res) => {
    const position = await this.issService.fetchAndStorePosition();
    
    if (!position) {
      throw new ApiError(500, 'Failed to fetch and store ISS position');
    }
    
    res.json({
      id: position.id,
      fetched_at: position.fetched_at,
      source_url: position.source_url,
      payload: position.payload
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