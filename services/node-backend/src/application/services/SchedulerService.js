const cron = require('node-cron');

class SchedulerService {
  constructor(issService, osdrService, pool) {
    this.issService = issService;
    this.osdrService = osdrService;
    this.pool = pool;
  }

  async withLock(lockId, task) {
    const client = await this.pool.connect();
    
    try {
      const lockResult = await client.query(
        'SELECT pg_try_advisory_lock($1) as locked',
        [lockId]
      );
      
      if (!lockResult.rows[0].locked) {
        console.log(`Lock ${lockId} is already taken, skipping execution`);
        return;
      }
      
      try {
        await task();
      } finally {
        await client.query('SELECT pg_advisory_unlock($1)', [lockId]);
      }
    } finally {
      client.release();
    }
  }

  start() {
    cron.schedule('*/2 * * * *', async () => {
      await this.withLock(1001, async () => {
        console.log('Fetching ISS position...');
        try {
          await this.issService.fetchAndStorePosition();
          console.log('ISS position fetched successfully');
        } catch (error) {
          console.error('Error fetching ISS:', error.message);
        }
      });
    });

    cron.schedule('*/10 * * * *', async () => {
      await this.withLock(1002, async () => {
        console.log('Fetching OSDR data...');
        try {
          await this.osdrService.fetchAndStoreOsdr();
          console.log('OSDR data fetched successfully');
        } catch (error) {
          console.error('Error fetching OSDR:', error.message);
        }
      });
    });

    cron.schedule('0 */12 * * *', async () => {
      await this.withLock(1003, async () => {
        console.log('Fetching APOD...');
        try {
          await this.osdrService.fetchApod();
          console.log('APOD fetched successfully');
        } catch (error) {
          console.error('Error fetching APOD:', error.message);
        }
      });
    });

    cron.schedule('0 */2 * * *', async () => {
      await this.withLock(1004, async () => {
        console.log('Fetching NEO feed...');
        try {
          await this.osdrService.fetchNeoFeed();
          console.log('NEO feed fetched successfully');
        } catch (error) {
          console.error('Error fetching NEO:', error.message);
        }
      });
    });

    cron.schedule('0 * * * *', async () => {
      await this.withLock(1005, async () => {
        console.log('Fetching DONKI FLR...');
        try {
          await this.osdrService.fetchDonkiFlr();
          console.log('DONKI FLR fetched successfully');
        } catch (error) {
          console.error('Error fetching DONKI FLR:', error.message);
        }
      });
    });

    cron.schedule('30 * * * *', async () => {
      await this.withLock(1006, async () => {
        console.log('Fetching DONKI CME...');
        try {
          await this.osdrService.fetchDonkiCme();
          console.log('DONKI CME fetched successfully');
        } catch (error) {
          console.error('Error fetching DONKI CME:', error.message);
        }
      });
    });

    cron.schedule('0 * * * *', async () => {
      await this.withLock(1007, async () => {
        console.log('Fetching SpaceX launch...');
        try {
          await this.osdrService.fetchSpacexNext();
          console.log('SpaceX launch fetched successfully');
        } catch (error) {
          console.error('Error fetching SpaceX:', error.message);
        }
      });
    });

    console.log('Scheduler started with all background tasks');
  }
}

module.exports = SchedulerService;