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

  async delete(key) {
    try {
      return await this.redisClient.del(key);
    } catch (error) {
      console.error('Redis delete error:', error.message);
      return 0;
    }
  }

  async deleteAll(keys) {
    try {
      if (keys.length === 0) return 0;
      return await this.redisClient.del(keys);
    } catch (error) {
      console.error('Redis deleteAll error:', error.message);
      return 0;
    }
  }

  async keys(pattern) {
    try {
      return await this.redisClient.keys(pattern);
    } catch (error) {
      console.error('Redis keys error:', error.message);
      return [];
    }
  }

  async getJson(key) {
    try {
      const data = await this.redisClient.get(key);
      return data ? JSON.parse(data) : null;
    } catch (error) {
      console.error('Redis getJson error:', error.message);
      return null;
    }
  }

  async setJson(key, value, ttlSeconds = 3600) {
    try {
      const jsonValue = JSON.stringify(value);
      await this.redisClient.set(key, jsonValue, { EX: ttlSeconds });
      return true;
    } catch (error) {
      console.error('Redis setJson error:', error.message);
      return false;
    }
  }

  async ping() {
    try {
      return await this.redisClient.ping();
    } catch (error) {
      console.error('Redis ping error:', error.message);
      return null;
    }
  }
}

module.exports = CacheRepository;