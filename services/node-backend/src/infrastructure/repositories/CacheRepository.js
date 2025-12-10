class CacheRepository {
  constructor(redisClient, pool) {
    this.redisClient = redisClient;
    this.pool = pool;
  }

  async get(key) {
    try {
      return await this.redisClient.get(key);
    } catch (error) {
      console.error('Redis get error:', error.message);
      return null;
    }
  }

  async set(key, value, ttlSeconds = 3600) {
    try {
      await this.redisClient.set(key, value, { EX: ttlSeconds });
    } catch (error) {
      console.error('Redis set error:', error.message);
    }
  }

  async insertCache(source, payload) {
    try {
      const query = `
        INSERT INTO space_cache (source, payload)
        VALUES ($1, $2)
        RETURNING *
      `;
      
      const result = await this.pool.query(query, [source, payload]);
      return result.rows[0];
    } catch (error) {
      console.error('Error inserting into space_cache:', error.message);
      throw error;
    }
  }

  async getLatestFromCache(source) {
    try {
      const query = `
        SELECT fetched_at, payload 
        FROM space_cache 
        WHERE source = $1 
        ORDER BY id DESC 
        LIMIT 1
      `;
      
      const result = await this.pool.query(query, [source]);
      return result.rows[0];
    } catch (error) {
      console.error('Error getting from space_cache:', error.message);
      return null;
    }
  }

  async getSpaceSummary() {
    try {
      const sources = ['apod', 'neo', 'flr', 'cme', 'spacex'];
      const summary = {};
      
      for (const source of sources) {
        const data = await this.getLatestFromCache(source);
        if (data) {
          summary[source] = {
            at: data.fetched_at,
            payload: data.payload
          };
        } else {
          summary[source] = {};
        }
      }
      
      return summary;
    } catch (error) {
      console.error('Error getting space summary:', error.message);
      return {};
    }
  }
}

module.exports = CacheRepository;