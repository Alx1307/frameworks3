const BaseValidator = require('./base.validator');

class IssDataValidator extends BaseValidator {
  constructor() {
    super();
  }

  validateLatestParams(params) {
    this.errors = [];
    
    if (params.limit !== undefined) {
      this.isNumber(params.limit, 'limit', 1, 1000);
    }
    
    if (params.hours !== undefined) {
      this.isNumber(params.hours, 'hours', 1, 720);
    }
    
    if (this.isValid()) {
      this.validatedData = {
        limit: params.limit ? parseInt(params.limit) : null,
        hours: params.hours ? parseInt(params.hours) : null
      };
    }
    
    return this;
  }

  validateHistoryParams(params) {
    this.errors = [];
    
    if (params.limit !== undefined) {
      this.isNumber(params.limit, 'limit', 1, 1000);
    }
    
    if (params.start_date !== undefined) {
      this.isTimestamp(params.start_date, 'start_date');
    }
    
    if (params.end_date !== undefined) {
      this.isTimestamp(params.end_date, 'end_date');
    }
    
    if (params.min_lat !== undefined) {
      this.isInRange(params.min_lat, 'min_lat', -90, 90);
    }
    
    if (params.max_lat !== undefined) {
      this.isInRange(params.max_lat, 'max_lat', -90, 90);
    }
    
    if (params.min_lon !== undefined) {
      this.isInRange(params.min_lon, 'min_lon', -180, 180);
    }
    
    if (params.max_lon !== undefined) {
      this.isInRange(params.max_lon, 'max_lon', -180, 180);
    }
    
    if (params.min_lat !== undefined && params.max_lat !== undefined) {
      if (parseFloat(params.min_lat) > parseFloat(params.max_lat)) {
        this.addError('lat_range', 'min_lat cannot be greater than max_lat');
      }
    }
    
    if (params.min_lon !== undefined && params.max_lon !== undefined) {
      if (parseFloat(params.min_lon) > parseFloat(params.max_lon)) {
        this.addError('lon_range', 'min_lon cannot be greater than max_lon');
      }
    }
    
    if (this.isValid()) {
      this.validatedData = {
        limit: params.limit ? parseInt(params.limit) : 50,
        start_date: params.start_date ? new Date(params.start_date).toISOString() : null,
        end_date: params.end_date ? new Date(params.end_date).toISOString() : null,
        min_lat: params.min_lat ? parseFloat(params.min_lat) : null,
        max_lat: params.max_lat ? parseFloat(params.max_lat) : null,
        min_lon: params.min_lon ? parseFloat(params.min_lon) : null,
        max_lon: params.max_lon ? parseFloat(params.max_lon) : null
      };
    }
    
    return this;
  }

  validateTrendParams(params) {
    this.errors = [];
    
    if (params.hours !== undefined) {
      this.isNumber(params.hours, 'hours', 1, 168);
    }
    
    if (params.limit !== undefined) {
      this.isNumber(params.limit, 'limit', 1, 1000);
    }
    
    if (params.interval !== undefined) {
      this.isString(params.interval, 'interval');
      const validIntervals = ['hour', 'day', 'week'];
      if (!validIntervals.includes(params.interval)) {
        this.addError('interval', `interval must be one of: ${validIntervals.join(', ')}`);
      }
    }
    
    if (this.isValid()) {
      this.validatedData = {
        hours: params.hours ? parseInt(params.hours) : null,
        limit: params.limit ? parseInt(params.limit) : null,
        interval: params.interval || 'hour'
      };
    }
    
    return this;
  }

  validateHaversineParams(params) {
    this.errors = [];
    
    this.isRequired(params.lat1, 'lat1');
    this.isRequired(params.lon1, 'lon1');
    this.isRequired(params.lat2, 'lat2');
    this.isRequired(params.lon2, 'lon2');
    
    if (params.lat1 !== undefined) {
      this.isInRange(params.lat1, 'lat1', -90, 90);
    }
    
    if (params.lon1 !== undefined) {
      this.isInRange(params.lon1, 'lon1', -180, 180);
    }
    
    if (params.lat2 !== undefined) {
      this.isInRange(params.lat2, 'lat2', -90, 90);
    }
    
    if (params.lon2 !== undefined) {
      this.isInRange(params.lon2, 'lon2', -180, 180);
    }
    
    if (this.isValid()) {
      this.validatedData = {
        lat1: parseFloat(params.lat1),
        lon1: parseFloat(params.lon1),
        lat2: parseFloat(params.lat2),
        lon2: parseFloat(params.lon2)
      };
    }
    
    return this;
  }

  validateIssApiData(data) {
    this.errors = [];
    
    this.isRequired(data.latitude, 'latitude');
    this.isRequired(data.longitude, 'longitude');
    this.isRequired(data.altitude, 'altitude');
    this.isRequired(data.velocity, 'velocity');
    this.isRequired(data.visibility, 'visibility');
    this.isRequired(data.timestamp, 'timestamp');
    
    if (data.latitude !== undefined) {
      this.isInRange(data.latitude, 'latitude', -90, 90);
    }
    
    if (data.longitude !== undefined) {
      this.isInRange(data.longitude, 'longitude', -180, 180);
    }
    
    if (data.altitude !== undefined) {
      this.isInRange(data.altitude, 'altitude', 0, 1000);
    }
    
    if (data.velocity !== undefined) {
      this.isInRange(data.velocity, 'velocity', 0, 30000);
    }
    
    if (data.visibility !== undefined) {
      this.isString(data.visibility, 'visibility');
      const validVisibilities = ['daylight', 'eclipse', 'na'];
      if (!validVisibilities.includes(data.visibility)) {
        this.addError('visibility', `visibility must be one of: ${validVisibilities.join(', ')}`);
      }
    }
    
    if (data.timestamp !== undefined) {
      this.isTimestamp(data.timestamp, 'timestamp');
    }
    
    if (data.solar_lat !== undefined) {
      this.isInRange(data.solar_lat, 'solar_lat', -90, 90);
    }
    
    if (data.solar_lon !== undefined) {
      this.isInRange(data.solar_lon, 'solar_lon', -180, 180);
    }
    
    if (data.units !== undefined) {
      this.isString(data.units, 'units');
      const validUnits = ['kilometers', 'miles', 'nautical'];
      if (!validUnits.includes(data.units)) {
        this.addError('units', `units must be one of: ${validUnits.join(', ')}`);
      }
    }
    
    if (this.isValid()) {
      this.validatedData = {
        latitude: parseFloat(data.latitude),
        longitude: parseFloat(data.longitude),
        altitude: parseFloat(data.altitude),
        velocity: parseFloat(data.velocity),
        visibility: data.visibility,
        timestamp: new Date(data.timestamp).toISOString(),
        solar_lat: data.solar_lat ? parseFloat(data.solar_lat) : null,
        solar_lon: data.solar_lon ? parseFloat(data.solar_lon) : null,
        units: data.units || 'kilometers'
      };
    }
    
    return this;
  }
}

module.exports = IssDataValidator;