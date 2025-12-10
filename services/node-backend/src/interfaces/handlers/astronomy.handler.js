const asyncHandler = require('express-async-handler');
const ApiError = require('../../shared/errors/ApiError');

class AstronomyHandler {
  constructor(astronomyService) {
    this.astronomyService = astronomyService;
  }

  getEvents = asyncHandler(async (req, res) => {
    const { lat, lon, days } = req.query;
    
    const latitude = parseFloat(lat || '55.7558');
    const longitude = parseFloat(lon || '37.6176');
    const daysCount = Math.max(1, Math.min(30, parseInt(days || '7')));
    
    if (isNaN(latitude) || latitude < -90 || latitude > 90) {
      throw new ApiError(400, 'Latitude must be between -90 and 90');
    }
    
    if (isNaN(longitude) || longitude < -180 || longitude > 180) {
      throw new ApiError(400, 'Longitude must be between -180 and 180');
    }
    
    const events = await this.astronomyService.getEvents(latitude, longitude, daysCount);
    
    res.json(events);
  });

  // Заменяем этот метод на новый с информацией об альтернативных API
  getConfig = asyncHandler(async (req, res) => {
    const hasConfig = !!(process.env.ASTRO_APP_ID && process.env.ASTRO_APP_SECRET);
    
    res.json({
        configured: hasConfig,
        api_key_present: !!process.env.ASTRO_APP_ID,
        api_secret_present: !!process.env.ASTRO_APP_SECRET,
        alternative_apis: {
            open_notify: "http://api.open-notify.org",
            sunrise_sunset: "https://api.sunrise-sunset.org",
            status: "Active (free, no API key required)"
        },
        message: hasConfig 
            ? 'Astronomy API is configured (using AstronomyAPI)' 
            : 'Using free alternative APIs (Open Notify & Sunrise Sunset)'
    });
  });

  // Тестовый эндпоинт для проверки API ключей
  testApi = asyncHandler(async (req, res) => {
    const { lat, lon, days } = req.query;
    
    const latitude = parseFloat(lat || '55.7558');
    const longitude = parseFloat(lon || '37.6176');
    const daysCount = Math.max(1, Math.min(30, parseInt(days || '1')));
    
    // Пробуем прямой запрос с логированием
    const from = new Date().toISOString().split('T')[0];
    const toDate = new Date();
    toDate.setDate(toDate.getDate() + parseInt(daysCount));
    const to = toDate.toISOString().split('T')[0];
    
    const credentials = `${process.env.ASTRO_APP_ID}:${process.env.ASTRO_APP_SECRET}`;
    const auth = Buffer.from(credentials).toString('base64');
    
    const params = new URLSearchParams({
      latitude: latitude,
      longitude: longitude,
      from_date: from,
      to_date: to,
      elevation: 0
    });
    
    const url = `https://api.astronomyapi.com/api/v2/bodies/events?${params.toString()}`;
    
    console.log('Testing Astronomy API...');
    console.log('API Key present:', !!process.env.ASTRO_APP_ID);
    console.log('API Secret present:', !!process.env.ASTRO_APP_SECRET);
    console.log('Credentials length:', credentials.length);
    console.log('Request URL (credentials hidden):', 
                url.replace(process.env.ASTRO_APP_ID || '', '***')
                   .replace(process.env.ASTRO_APP_SECRET || '', '***'));
    
    try {
      const response = await fetch(url, {
        method: 'GET',
        headers: {
          'Authorization': `Basic ${auth}`,
          'Accept': 'application/json'
        },
        timeout: 10000
      });
      
      const responseText = await response.text();
      
      res.json({
        success: response.ok,
        status: response.status,
        statusText: response.statusText,
        url: url.replace(process.env.ASTRO_APP_ID || '', '***')
               .replace(process.env.ASTRO_APP_SECRET || '', '***'),
        auth_header_present: !!auth,
        credentials_info: {
          api_key_present: !!process.env.ASTRO_APP_ID,
          api_secret_present: !!process.env.ASTRO_APP_SECRET,
          api_key_length: process.env.ASTRO_APP_ID?.length || 0,
          api_secret_length: process.env.ASTRO_APP_SECRET?.length || 0,
          credentials_length: credentials.length
        },
        response_preview: responseText.substring(0, 500) + (responseText.length > 500 ? '...' : '')
      });
      
    } catch (error) {
      res.json({
        success: false,
        error: error.message,
        url: url.replace(process.env.ASTRO_APP_ID || '', '***')
               .replace(process.env.ASTRO_APP_SECRET || '', '***'),
        credentials: {
          api_key_present: !!process.env.ASTRO_APP_ID,
          api_secret_present: !!process.env.ASTRO_APP_SECRET,
          api_key_length: process.env.ASTRO_APP_ID?.length || 0,
          api_secret_length: process.env.ASTRO_APP_SECRET?.length || 0
        }
      });
    }
  });
}

module.exports = AstronomyHandler;