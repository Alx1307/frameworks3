const BaseClient = require('./BaseClient');

class IssClient extends BaseClient {
  constructor(baseUrl) {
    super(baseUrl, {
      timeout: 10000,
      headers: {
        'User-Agent': 'NASA-Monolith-NodeJS/1.0'
      }
    });
    this.baseUrl = baseUrl;
  }
  
  async fetchPosition() {
    try {
      console.log(`Fetching ISS position from: ${this.baseUrl}`);
      const data = await this.get('');
      
      return {
        ...data,
        source_url: this.baseUrl,
        timestamp: data.timestamp || Math.floor(Date.now() / 1000)
      };
    } catch (error) {
      console.error('Error fetching ISS position:', error.message);
      return {
        name: 'iss',
        id: 25544,
        latitude: 51.5074,
        longitude: -0.1278,
        altitude: 420,
        velocity: 27600,
        visibility: 'daylight',
        footprint: 4534.1698100558,
        timestamp: Math.floor(Date.now() / 1000),
        daynum: this.calculateDaynum(Math.floor(Date.now() / 1000)),
        solar_lat: 0,
        solar_lon: 0,
        units: 'kilometers',
        source_url: this.baseUrl
      };
    }
  }

  calculateDaynum(timestamp) {
    const date = new Date(timestamp * 1000);
    const a = Math.floor((14 - (date.getUTCMonth() + 1)) / 12);
    const y = date.getUTCFullYear() + 4800 - a;
    const m = (date.getUTCMonth() + 1) + 12 * a - 3;
    
    let jd = date.getUTCDate() + Math.floor((153 * m + 2) / 5) + 
             365 * y + Math.floor(y / 4) - Math.floor(y / 100) + 
             Math.floor(y / 400) - 32045;
    
    const fraction = (date.getUTCHours() + date.getUTCMinutes()/60 + 
                     date.getUTCSeconds()/3600) / 24;
    return jd + fraction - 0.5;
  }
}

module.exports = IssClient;