class JwstService {
  constructor(jwstRepo, jwstClient, cacheRepo) {
      this.jwstRepo = jwstRepo;
      this.jwstClient = jwstClient;
      this.cacheRepo = cacheRepo;
  }

  async getImages(filters = {}) {
      const { source, suffix, program, instrument, perPage = 24, page = 1 } = filters;
      const cacheKey = `jwst_images:${JSON.stringify(filters)}`;

      const dbCount = await this.jwstRepo.getImageCount();
      console.log(`Total images in database: ${dbCount}`);

      if (dbCount < 10) {
        console.log('Database has few images, fetching fresh data...');
        try {
          await this.fetchAndCacheImages(50);
        } catch (error) {
          console.error('Failed to fetch initial images:', error.message);
        }
      }

      try {
          const cached = await this.cacheRepo.get(cacheKey);
          if (cached) {
              console.log('Cache HIT for key:', cacheKey);
              return JSON.parse(cached);
          }
      } catch (cacheError) {
          console.warn('Redis cache check failed, proceeding to API:', cacheError.message);
      }

      if (suffix || program || instrument) {
          const dbFilters = { suffix, program, instrument, limit: perPage, offset: (page - 1) * perPage };
          const dbResult = await this.jwstRepo.getCachedImages(dbFilters);

          if (dbResult.items.length > 0) {
              await this._cacheResult(cacheKey, dbResult);
              return dbResult;
          }
      }

      const apiParams = { type: source || 'jpg', page, perPage, program, instrument };
      const apiResult = await this.jwstClient.getImages(apiParams);

      let finalResult;
      if (apiResult.success) {
          const processedItems = this._processApiItems(apiResult.data.items);
          finalResult = {
              items: processedItems,
              total: apiResult.data.total,
              page: apiResult.data.page,
              limit: apiResult.data.limit,
              hasMore: apiResult.data.hasMore
          };
          await this.jwstRepo.cacheImages(processedItems);
      } else {
          console.error('API request failed, returning empty result.');
          finalResult = { items: [], total: 0, page, limit: perPage, hasMore: false };
      }

      await this._cacheResult(cacheKey, finalResult);
      return finalResult;
  }

  _processApiItems(apiItems) {
      if (!apiItems || !Array.isArray(apiItems)) return [];

      return apiItems.map(item => {
          const instrumentObj = item.details?.instruments?.[0];
          return {
              jwst_id: item.id,
              url: item.location || '',
              thumbnail: item.thumbnail || '',
              title: item.id,
              caption: item.details?.description || '',
              instrument: instrumentObj?.instrument || 'UNKNOWN',
              program_id: item.program?.toString() || '',
              suffix: item.details?.suffix || ''
          };
      });
  }

  async fetchAndCacheImages(limit = 100) {
      console.log('Service: Fetching and caching images from API...');
      const apiResult = await this.jwstClient.getAllImages('jpg', limit);

      if (!apiResult.success || !apiResult.data?.body) {
          console.log('Service: No data received from API.');
          return [];
      }

      const itemsToCache = this._processApiItems(apiResult.data.body);
      console.log(`Service: Processed ${itemsToCache.length} items for caching.`);

      if (itemsToCache.length > 0) {
          const savedCount = await this.jwstRepo.cacheImages(itemsToCache);
          console.log(`Service: Successfully cached ${savedCount} images in database.`);
      }

      return itemsToCache;
  }

  async getInstrumentStats() {
      const cacheKey = 'jwst_instrument_stats';
      try {
          const cached = await this.cacheRepo.get(cacheKey);
          if (cached) return JSON.parse(cached);
      } catch (error) {
          console.warn('Instrument stats cache failed:', error.message);
      }

      try {
          const stats = await this.jwstRepo.getInstrumentStats();
          await this.cacheRepo.set(cacheKey, JSON.stringify(stats), 3600);
          return stats;
      } catch (error) {
          console.error('Error getting instrument stats from DB:', error);
          return {};
      }
  }

  async checkApiStatus() {
      return await this.jwstClient.checkHealth();
  }

  async _cacheResult(key, data) {
      try {
          await this.cacheRepo.set(key, JSON.stringify(data), 600);
      } catch (error) {
          console.warn(`Could not cache result for key ${key}:`, error.message);
      }
  }
}

module.exports = JwstService;