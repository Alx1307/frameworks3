const asyncHandler = require('express-async-handler');
const ApiError = require('../../shared/errors/ApiError');

class SpaceHandler {
  constructor(osdrService, cacheRepository, issRepo) {
    this.osdrService = osdrService;
    this.cacheRepository = cacheRepository;
    this.issRepo = issRepo;
  }

  getLatest = asyncHandler(async (req, res) => {
    const { src } = req.params;
    
    const validSources = ['apod', 'neo', 'flr', 'cme', 'spacex'];
    if (!validSources.includes(src.toLowerCase())) {
      throw new ApiError(400, `Invalid source. Must be one of: ${validSources.join(', ')}`);
    }
    
    const data = await this.cacheRepository.getLatestFromCache(src.toLowerCase());
    
    if (!data) {
      return res.json({ 
        source: src, 
        message: "no data" 
      });
    }
    
    res.json({
      source: src,
      fetched_at: data.fetched_at,
      payload: data.payload
    });
  });

  refresh = asyncHandler(async (req, res) => {
    const { src } = req.query;
    
    let sources = ['apod', 'neo', 'flr', 'cme', 'spacex'];
    if (src) {
      sources = src.split(',').map(s => s.trim().toLowerCase());
    }
    
    const results = await this.osdrService.refreshAllSources(sources);
    
    res.json({ refreshed: Object.keys(results) });
  });

  getSummary = asyncHandler(async (req, res) => {
    const cacheSummary = await this.cacheRepository.getSpaceSummary();
    
    let issData = {};
    try {
      const latestIss = await this.issRepo.getLatestPosition();
      if (latestIss) {
        issData = {
          at: latestIss.fetched_at,
          payload: latestIss.payload
        };
      }
    } catch (error) {
      console.error('Error getting ISS data for summary:', error.message);
    }
    
    const osdrCount = await this.osdrService.getOsdrCount();
    
    res.json({
      ...cacheSummary,
      iss: issData,
      osdr_count: osdrCount
    });
  });
}

module.exports = SpaceHandler;