const express = require('express');
const helmet = require('helmet');
const cors = require('cors');
const { Pool } = require('pg');
const redis = require('redis');
const { apiLimiter } = require('./src/config/rate-limit');

const AppState = {
  init: async () => {
    const pool = new Pool({
      connectionString: process.env.DATABASE_URL,
      max: 10,
      idleTimeoutMillis: 30000,
      connectionTimeoutMillis: 2000,
    });

    const redisClient = redis.createClient({
      url: process.env.REDIS_URL || 'redis://redis:6379'
    });
    
    try {
      await redisClient.connect();
      console.log('Redis connected successfully');
    } catch (err) {
      console.error('Redis connection failed:', err.message);
    }

    const IssRepository = require('./src/infrastructure/repositories/IssRepository');
    const OsdrRepository = require('./src/infrastructure/repositories/OsdrRepository');
    const CacheRepository = require('./src/infrastructure/repositories/CacheRepository');
    
    const issRepo = new IssRepository(pool);
    const osdrRepo = new OsdrRepository(pool);
    const cacheRepo = new CacheRepository(redisClient, pool);

    const NasaClient = require('./src/infrastructure/clients/NasaClient');
    const IssClient = require('./src/infrastructure/clients/IssClient');
    
    const nasaClient = new NasaClient(
      process.env.NASA_API_URL || 'https://visualization.osdr.nasa.gov/biodata/api/v2/datasets/',
      process.env.NASA_API_KEY
    );
    const issClient = new IssClient(process.env.WHERE_ISS_URL || 'https://api.wheretheiss.at/v1/satellites/25544');

    const IssService = require('./src/application/services/IssService');
    const OsdrService = require('./src/application/services/OsdrService');
    const AstronomyService = require('./src/application/services/AstronomyService');
    
    const issService = new IssService(issRepo, issClient);
    const osdrService = new OsdrService(osdrRepo, nasaClient, cacheRepo);
    const astronomyService = new AstronomyService(cacheRepo);

    const SchedulerService = require('./src/application/services/SchedulerService');
    const scheduler = new SchedulerService(issService, osdrService, pool);
    scheduler.start();

    return {
      pool,
      redisClient,
      issService,
      osdrService,
      astronomyService,
      cacheRepo,
      issRepo
    };
  }
};

async function createApp() {
  const app = express();
  
  app.use(helmet());
  app.use(cors());
  app.use(express.json());

  app.use((req, res, next) => {
    console.log(`[${new Date().toISOString()}] ${req.method} ${req.path}`);
    next();
  });
  
  app.use('/api', apiLimiter);
  
  const state = await AppState.init();
  
  const IssHandler = require('./src/interfaces/handlers/iss.handler');
  const OsdrHandler = require('./src/interfaces/handlers/osdr.handler');
  const SpaceHandler = require('./src/interfaces/handlers/space.handler');
  const HealthHandler = require('./src/interfaces/handlers/health.handler');
  const AstronomyHandler = require('./src/interfaces/handlers/astronomy.handler');
  
  const issHandler = new IssHandler(state.issService);
  const osdrHandler = new OsdrHandler(state.osdrService);
  const spaceHandler = new SpaceHandler(state.osdrService, state.cacheRepo, state.issRepo);
  const healthHandler = new HealthHandler(state.pool, state.redisClient);
  const astronomyHandler = new AstronomyHandler(state.astronomyService);
  
  const healthRouter = require('./src/interfaces/routes/health.routes')(healthHandler);
  const issRouter = require('./src/interfaces/routes/iss.routes')(issHandler);
  const osdrRouter = require('./src/interfaces/routes/osdr.routes')(osdrHandler);
  const spaceRouter = require('./src/interfaces/routes/space.routes')(spaceHandler);
  const astronomyRouter = require('./src/interfaces/routes/astronomy.routes')(astronomyHandler);
  
  app.use('/api/health', healthRouter);
  app.use('/api/iss', issRouter);
  app.use('/api/osdr', osdrRouter);
  app.use('/api/space', spaceRouter);
  app.use('/api/astronomy', astronomyRouter);

  app.use((req, res) => {
    res.status(404).json({
      error: {
        message: `Route ${req.method} ${req.path} not found`,
        timestamp: new Date().toISOString()
      }
    });
  });
  
  app.use(require('./src/application/middlewares/errorHandler'));
  
  return app;
}

if (require.main === module) {
  createApp().then(app => {
    const PORT = process.env.PORT || 3001;
    app.listen(PORT, () => {
      console.log(`Node.js backend running on port ${PORT}`);
      console.log(`Environment: ${process.env.NODE_ENV || 'development'}`);
      console.log(`Health check: http://localhost:${PORT}/api/health`);
      console.log(`ISS latest: http://localhost:${PORT}/api/iss/latest`);
      console.log(`OSDR list: http://localhost:${PORT}/api/osdr/list`);
      console.log(`Space summary: http://localhost:${PORT}/api/space/summary`);
      console.log(`Astronomy events: http://localhost:${PORT}/api/astronomy/events?lat=55.7558&lon=37.6176&days=7`);
    });
  }).catch(err => {
    console.error('Failed to start application:', err);
    process.exit(1);
  });
}

module.exports = createApp;