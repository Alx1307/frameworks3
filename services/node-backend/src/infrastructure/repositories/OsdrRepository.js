const OsdrItem = require('../../domain/entities/OsdrItem');

class OsdrRepository {
  constructor(pool) {
    this.pool = pool;
  }

  async upsertItems(items) {
    const results = [];
    
    for (const item of items) {
      try {
        const id = OsdrItem.sPick(item, [
          'dataset_id', 'id', 'uuid', 'studyId', 'accession', 'osdr_id'
        ]);
        
        const title = OsdrItem.sPick(item, ['title', 'name', 'label']);
        const status = OsdrItem.sPick(item, ['status', 'state', 'lifecycle']);
        const updated = OsdrItem.tPick(item, [
          'updated', 'updated_at', 'modified', 'lastUpdated', 'timestamp'
        ]);

        if (id) {
          const query = `
            INSERT INTO osdr_items (dataset_id, title, status, updated_at, raw)
            VALUES ($1, $2, $3, $4, $5)
            ON CONFLICT (dataset_id) DO UPDATE
            SET title = EXCLUDED.title,
                status = EXCLUDED.status,
                updated_at = EXCLUDED.updated_at,
                raw = EXCLUDED.raw
            RETURNING *
          `;
          
          const values = [id, title, status, updated, item];
          const result = await this.pool.query(query, values);
          results.push(OsdrItem.fromDatabase(result.rows[0]));
        } else {
          const query = `
            INSERT INTO osdr_items (dataset_id, title, status, updated_at, raw)
            VALUES ($1, $2, $3, $4, $5)
            RETURNING *
          `;
          
          const values = [null, title, status, updated, item];
          const result = await this.pool.query(query, values);
          results.push(OsdrItem.fromDatabase(result.rows[0]));
        }
      } catch (error) {
        console.error('Error upserting OSDR item:', error.message);
      }
    }
    
    return results;
  }

  async getItems(limit = 20) {
    const query = `
      SELECT id, dataset_id, title, status, updated_at, inserted_at, raw
      FROM osdr_items
      ORDER BY inserted_at DESC
      LIMIT $1
    `;
    
    const result = await this.pool.query(query, [limit]);
    return result.rows.map(row => OsdrItem.fromDatabase(row));
  }

  async getCount() {
    const query = `SELECT COUNT(*) as count FROM osdr_items`;
    const result = await this.pool.query(query);
    return parseInt(result.rows[0].count, 10);
  }

  async cleanupOldItems(days = 30) {
    const query = `
      DELETE FROM osdr_items 
      WHERE inserted_at < NOW() - INTERVAL '${days} days'
    `;
    const result = await this.pool.query(query);
    return result.rowCount;
  }
}

module.exports = OsdrRepository;