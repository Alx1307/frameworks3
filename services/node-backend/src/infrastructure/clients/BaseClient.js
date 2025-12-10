const axios = require('axios');
const { setTimeout } = require('timers/promises');

class BaseClient {
  constructor(baseURL, config = {}) {
    this.client = axios.create({
      baseURL,
      timeout: config.timeout || 30000,
      headers: {
        'User-Agent': 'NASA-Monolith-Backend/1.0',
        ...config.headers
      }
    });

    this.client.interceptors.response.use(
      response => response,
      async (error) => {
        const config = error.config;
        
        if (!config || !config.retry) {
          config.retry = 0;
        }
        
        if (config.retry >= (config.maxRetries || 3)) {
          return Promise.reject(error);
        }
        
        config.retry += 1;
        
        const delay = Math.min(1000 * Math.pow(2, config.retry), 30000);
        await setTimeout(delay);
        
        return this.client(config);
      }
    );
  }

  async get(url, params = {}, options = {}) {
    try {
      const response = await this.client.get(url, {
        params,
        ...options
      });
      return response.data;
    } catch (error) {
      this.handleError(error, url);
    }
  }

  handleError(error, url) {
    if (error.response) {
      throw new Error(
        `API Error ${error.response.status}: ${url} - ${error.response.data?.message || 'Unknown error'}`
      );
    } else if (error.request) {
      throw new Error(`Network Error: ${url} - No response received`);
    } else {
      throw new Error(`Request Error: ${url} - ${error.message}`);
    }
  }
}

module.exports = BaseClient;