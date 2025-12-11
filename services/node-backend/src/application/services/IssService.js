class IssService {
  constructor(issRepository, issClient) {
    this.issRepository = issRepository;
    this.issClient = issClient;
  }
  
  async getLatestPosition() {
    const position = await this.issRepository.getLatestPosition();
    
    if (!position) {
      console.log('No ISS data in DB, fetching fresh data');
      return await this.fetchAndStorePosition();
    }
    
    return position;
  }
  
  async fetchAndStorePosition() {
    console.log('Fetching fresh ISS position from API');
    const positionData = await this.issClient.fetchPosition();
    
    const savedPosition = await this.issRepository.insertPosition(positionData);
    console.log('ISS position saved to database');
    
    return savedPosition;
  }

  async getTrend() {
    const trendData = await this.issRepository.getTrendData();
    
    if (!trendData) {
      return {
        movement: false,
        delta_km: 0.0,
        dt_sec: 0.0,
        velocity_kmh: null,
        from_time: null,
        to_time: null,
        from_lat: null,
        from_lon: null,
        to_lat: null,
        to_lon: null
      };
    }
    
    const { p1, p2, t1, t2 } = trendData;
    
    const lat1 = p1.latitude;
    const lon1 = p1.longitude;
    const lat2 = p2.latitude;
    const lon2 = p2.longitude;
    const velocity = p1.velocity;
    
    let delta_km = 0.0;
    let movement = false;
    
    if (lat1 !== undefined && lon1 !== undefined && lat2 !== undefined && lon2 !== undefined) {
      delta_km = this.calculateHaversine(lat1, lon1, lat2, lon2);
      movement = delta_km > 0.1;
    }
    
    const dt_sec = (t1 - t2) / 1000;
    
    return {
      movement,
      delta_km,
      dt_sec,
      velocity_kmh: velocity,
      from_time: t2,
      to_time: t1,
      from_lat: lat2,
      from_lon: lon2,
      to_lat: lat1,
      to_lon: lon1
    };
  }

  async getHistory(limit = 50) {
    try {
        const positions = await this.issRepository.getRecentPositions(limit);
        
        return {
            success: true,
            count: positions.length,
            points: positions.map(pos => ({
                lat: pos.payload.latitude,
                lon: pos.payload.longitude,
                altitude: pos.payload.altitude,
                velocity: pos.payload.velocity,
                timestamp: pos.payload.timestamp,
                fetched_at: pos.fetched_at
            }))
        };
    } catch (error) {
        console.error('Ошибка при загрузке истории МКС:', error);
        return {
            success: false,
            count: 0,
            points: []
        };
    }
  }
  
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

module.exports = IssService;