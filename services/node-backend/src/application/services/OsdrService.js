class OsdrService {
  constructor(osdrRepository, nasaClient, cacheRepository) {
    this.osdrRepository = osdrRepository;
    this.nasaClient = nasaClient;
    this.cacheRepository = cacheRepository;
  }

  async fetchAndStoreOsdr() {
    try {
      console.log('Fetching OSDR data from NASA API...');
      const data = await this.nasaClient.fetchOsdr();
      
      let items = [];
      
      if (Array.isArray(data)) {
        items = data;
      } else if (data.items && Array.isArray(data.items)) {
        items = data.items;
      } else if (data.results && Array.isArray(data.results)) {
        items = data.results;
      } else {
        items = [data];
      }
      
      console.log(`Processing ${items.length} OSDR items...`);
      
      const savedItems = await this.osdrRepository.upsertItems(items);
      
      console.log(`Successfully saved ${savedItems.length} OSDR items`);
      
      return {
        written: savedItems.length,
        total: items.length
      };
    } catch (error) {
      console.error('Error fetching OSDR:', error.message);
      
      return {
        written: 0,
        total: 0,
        error: error.message
      };
    }
  }

  async getOsdrList(limit = 20) {
    try {
      const items = await this.osdrRepository.getItems(limit);
      const count = await this.osdrRepository.getCount();
      
      return {
        items: items.map(item => item.toJSON()),
        count,
        limit
      };
    } catch (error) {
      console.error('Error getting OSDR list:', error.message);
      throw error;
    }
  }

  async getOsdrCount() {
    return await this.osdrRepository.getCount();
  }

  async fetchApod() {
    try {
      const cacheKey = 'apod:latest';
      const cached = await this.cacheRepository.get(cacheKey);
      
      if (cached) {
        console.log('Returning cached APOD');
        return JSON.parse(cached);
      }

      console.log('Fetching fresh APOD from NASA...');
      const apod = await this.nasaClient.fetchApod();
      
      await this.cacheRepository.set(cacheKey, JSON.stringify(apod), 12 * 3600);
      
      await this.cacheRepository.insertCache('apod', apod);
      
      console.log('APOD fetched and cached successfully');
      return apod;
    } catch (error) {
      console.error('Error fetching APOD:', error.message);
      throw error;
    }
  }

  async fetchNeoFeed() {
    try {
      const today = new Date().toISOString().split('T')[0];
      const startDate = new Date();
      startDate.setDate(startDate.getDate() - 2);
      const startDateStr = startDate.toISOString().split('T')[0];

      console.log(`Fetching NEO feed from ${startDateStr} to ${today}`);
      const neo = await this.nasaClient.fetchNeoFeed(startDateStr, today);
      
      await this.cacheRepository.insertCache('neo', neo);
      
      return neo;
    } catch (error) {
      console.error('Error fetching NEO:', error.message);
      throw error;
    }
  }

  async fetchDonkiFlr() {
    try {
      const today = new Date().toISOString().split('T')[0];
      const startDate = new Date();
      startDate.setDate(startDate.getDate() - 5);
      const startDateStr = startDate.toISOString().split('T')[0];

      console.log(`Fetching DONKI FLR from ${startDateStr} to ${today}`);
      const flr = await this.nasaClient.fetchDonki('FLR', startDateStr, today);
      
      await this.cacheRepository.insertCache('flr', flr);
      
      return flr;
    } catch (error) {
      console.error('Error fetching FLR:', error.message);
      throw error;
    }
  }

  async fetchDonkiCme() {
    try {
      const today = new Date().toISOString().split('T')[0];
      const startDate = new Date();
      startDate.setDate(startDate.getDate() - 5);
      const startDateStr = startDate.toISOString().split('T')[0];

      console.log(`Fetching DONKI CME from ${startDateStr} to ${today}`);
      const cme = await this.nasaClient.fetchDonki('CME', startDateStr, today);
      
      await this.cacheRepository.insertCache('cme', cme);
      
      return cme;
    } catch (error) {
      console.error('Error fetching CME:', error.message);
      throw error;
    }
  }

  async fetchSpacexNext() {
    try {
      const BaseClient = require('../../infrastructure/clients/BaseClient');
      const spacexClient = new BaseClient('https://api.spacexdata.com/v4', {
        timeout: 10000
      });
      
      console.log('Fetching next SpaceX launch...');
      const launch = await spacexClient.get('/launches/next');
      
      await this.cacheRepository.insertCache('spacex', launch);
      
      return launch;
    } catch (error) {
      console.error('Error fetching SpaceX:', error.message);
      throw error;
    }
  }

  async refreshAllSources(sources = ['apod', 'neo', 'flr', 'cme', 'spacex']) {
    const results = {};
    
    for (const source of sources) {
      try {
        console.log(`Refreshing ${source}...`);
        
        switch (source.toLowerCase()) {
          case 'apod':
            results.apod = await this.fetchApod();
            break;
          case 'neo':
            results.neo = await this.fetchNeoFeed();
            break;
          case 'flr':
            results.flr = await this.fetchDonkiFlr();
            break;
          case 'cme':
            results.cme = await this.fetchDonkiCme();
            break;
          case 'spacex':
            results.spacex = await this.fetchSpacexNext();
            break;
        }
        
        console.log(`${source} refreshed successfully`);
      } catch (error) {
        console.error(`Error refreshing ${source}:`, error.message);
        results[source] = { error: error.message };
      }
    }
    
    return results;
  }
}

module.exports = OsdrService;