class JwstHandler {
  constructor(jwstService) {
      this.jwstService = jwstService;
  }

  async getFeed(req, res) {
      try {
          const {
              source = 'jpg',
              suffix = '',
              program = '',
              instrument = '',
              perPage = '24',
              page = '1'
          } = req.query;

          const parsedPerPage = Math.min(parseInt(perPage), 100) || 24;
          const parsedPage = parseInt(page) || 1;

          if (parsedPerPage < 1 || parsedPerPage > 100) {
              return res.status(400).json({
                  success: false,
                  error: 'Parameter "perPage" must be between 1 and 100'
              });
          }
          if (parsedPage < 1) {
              return res.status(400).json({
                  success: false,
                  error: 'Parameter "page" must be greater than 0'
              });
          }

          const filters = {
              source,
              suffix: suffix.trim(),
              program: program.trim(),
              instrument: instrument.trim(),
              perPage: parsedPerPage,
              page: parsedPage
          };

          const result = await this.jwstService.getImages(filters);

          res.json({
              success: true,
              data: result,
              meta: {
                  source: filters.source,
                  suffix: filters.suffix,
                  program: filters.program,
                  instrument: filters.instrument,
                  page: filters.page,
                  perPage: filters.perPage,
                  total: result.total,
                  hasMore: result.hasMore
              }
          });

      } catch (error) {
          console.error('Error in JWST feed handler:', error);
          res.status(500).json({
              success: false,
              error: 'Internal server error while fetching JWST feed'
          });
      }
  }

  async getInstruments(req, res) {
      try {
          const stats = await this.jwstService.getInstrumentStats();
          res.json({
              success: true,
              data: stats,
              total: Object.keys(stats).length
          });
      } catch (error) {
          console.error('Error getting instruments:', error);
          res.status(500).json({
              success: false,
              error: 'Internal server error while fetching instrument stats'
          });
      }
  }

  async refreshCache(req, res) {
      try {
          const images = await this.jwstService.fetchAndCacheImages(100);
          res.json({
              success: true,
              message: `Cache refreshed successfully. Processed ${images.length} images.`,
              timestamp: new Date().toISOString()
          });
      } catch (error) {
          console.error('Error refreshing cache:', error);
          res.status(500).json({
              success: false,
              error: 'Failed to refresh cache'
          });
      }
  }

  async getHealth(req, res) {
      try {
          const apiStatus = await this.jwstService.checkApiStatus();

          const health = {
              success: true,
              status: apiStatus.connected ? 'operational' : 'degraded',
              api: apiStatus.connected ? 'connected' : 'disconnected',
              timestamp: new Date().toISOString(),
              environment: process.env.NODE_ENV || 'development'
          };

          if (!apiStatus.connected) {
              health.error = apiStatus.message;
          }

          res.json(health);

      } catch (error) {
          console.error('Error in JWST health check:', error);
          res.json({
              success: true,
              status: 'degraded',
              api: 'disconnected',
              timestamp: new Date().toISOString(),
              environment: process.env.NODE_ENV || 'development',
              error: error.message
          });
      }
  }

  async getStatus(req, res) {
      res.json({
          service: 'jwst-api',
          status: 'online',
          timestamp: new Date().toISOString()
      });
  }
}

module.exports = JwstHandler;