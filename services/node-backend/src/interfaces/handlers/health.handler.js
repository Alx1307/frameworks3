const asyncHandler = require('express-async-handler');

class HealthHandler {
  constructor(pool, redisClient) {
    this.pool = pool;
    this.redisClient = redisClient;
  }

  getHealth = asyncHandler(async (req, res) => {
    let dbStatus = 'healthy';
    try {
      await this.pool.query('SELECT 1 as health_check');
    } catch (error) {
      dbStatus = 'unhealthy';
      console.error('Database health check failed:', error.message);
    }

    let redisStatus = 'healthy';
    try {
      await this.redisClient.ping();
    } catch (error) {
      redisStatus = 'unhealthy';
      console.error('Redis health check failed:', error.message);
    }

    const overallStatus = dbStatus === 'healthy' && redisStatus === 'healthy' 
      ? 'healthy' 
      : 'degraded';

    res.json({
      status: overallStatus,
      timestamp: new Date().toISOString(),
      services: {
        database: {
          status: dbStatus,
          type: 'PostgreSQL'
        },
        cache: {
          status: redisStatus,
          type: 'Redis'
        }
      },
      version: '1.0.0',
      environment: process.env.NODE_ENV || 'development'
    });
  });
}

module.exports = HealthHandler;