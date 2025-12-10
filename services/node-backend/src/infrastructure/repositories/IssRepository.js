const { Pool } = require('pg');

class IssRepository {
  constructor(pool) {
    this.pool = pool;
  }

  async insertPosition(positionData) {
    const query = `
      INSERT INTO iss_fetch_log (source_url, payload)
      VALUES ($1, $2)
      RETURNING *
    `;
    
    const sourceUrl = positionData.source_url || positionData.sourceUrl;
    
    if (!sourceUrl) {
      console.warn('No source_url found in positionData, using default');
      console.log('Available keys:', Object.keys(positionData));
    }
    
    const values = [
      sourceUrl || 'unknown',
      JSON.stringify(positionData)
    ];

    const result = await this.pool.query(query, values);
    return this.fromDatabase(result.rows[0]);
  }

  async getLatestPosition() {
    const query = `
      SELECT * FROM iss_fetch_log 
      ORDER BY fetched_at DESC 
      LIMIT 1
    `;
    
    const result = await this.pool.query(query);
    return result.rows.length > 0 
      ? this.fromDatabase(result.rows[0])
      : null;
  }

  async getRecentPositions(limit = 2) {
    const query = `
      SELECT * FROM iss_fetch_log 
      ORDER BY fetched_at DESC 
      LIMIT $1
    `;
    
    const result = await this.pool.query(query, [limit]);
    return result.rows.map(row => this.fromDatabase(row));
  }

  async getTrendData() {
    const positions = await this.getRecentPositions(2);
    
    if (positions.length < 2) {
      return null;
    }
    
    const latest = positions[0];
    const previous = positions[1];
    
    return {
      latest,
      previous,
      p1: latest.payload,
      p2: previous.payload,
      t1: new Date(latest.fetched_at),
      t2: new Date(previous.fetched_at)
    };
  }

  fromDatabase(row) {
    return {
      id: row.id,
      fetched_at: row.fetched_at,
      source_url: row.source_url,
      payload: row.payload
    };
  }
}

module.exports = IssRepository;