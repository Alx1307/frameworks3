const BaseClient = require('./BaseClient');

class NasaClient extends BaseClient {
  constructor(baseURL, apiKey) {
    super(baseURL, {
      timeout: 30000,
      headers: {
        'Accept': 'application/json'
      }
    });
    this.apiKey = apiKey;
  }

  async fetchOsdr() {
    const params = {};
    if (this.apiKey) {
      params.api_key = this.apiKey;
    }
    return await this.get('', params);
  }

  async fetchApod(thumbs = true) {
    const params = { thumbs };
    if (this.apiKey) {
      params.api_key = this.apiKey;
    }
    return await this.get('https://api.nasa.gov/planetary/apod', params);
  }

  async fetchNeoFeed(startDate, endDate) {
    const params = { start_date: startDate, end_date: endDate };
    if (this.apiKey) {
      params.api_key = this.apiKey;
    }
    return await this.get('https://api.nasa.gov/neo/rest/v1/feed', params);
  }

  async fetchDonki(endpoint, startDate, endDate) {
    const params = { startDate, endDate };
    if (this.apiKey) {
      params.api_key = this.apiKey;
    }
    return await this.get(`https://api.nasa.gov/DONKI/${endpoint}`, params);
  }
}

module.exports = NasaClient;