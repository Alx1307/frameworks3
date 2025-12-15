const { Pool } = require('pg');

class JwstRepository {
  constructor(pool) {
    this.pool = pool;
  }

  async getImageCount() {
    try {
      const { rows } = await this.pool.query('SELECT COUNT(*) as count FROM jwst_images');
      return parseInt(rows[0].count) || 0;
    } catch (error) {
      console.error('Error getting image count:', error.message);
      return 0;
    }
  }

  async cacheImages(images) {
    if (!Array.isArray(images)) {
      images = [images];
    }

    if (images.length === 0) {
      return { success: true, saved: 0 };
    }

    const client = await this.pool.connect();
    let savedCount = 0;

    try {
      await client.query('BEGIN');
      
      for (const image of images) {
        try {
          const { rows } = await client.query(
            `INSERT INTO jwst_images (
              jwst_id, url, thumbnail, title, caption, 
              instrument, program_id, suffix
            ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)
            ON CONFLICT (jwst_id) DO UPDATE SET
              url = EXCLUDED.url,
              thumbnail = EXCLUDED.thumbnail,
              title = EXCLUDED.title,
              caption = EXCLUDED.caption,
              instrument = EXCLUDED.instrument,
              program_id = EXCLUDED.program_id,
              suffix = EXCLUDED.suffix,
              updated_at = CURRENT_TIMESTAMP
            RETURNING id`,
            [
              image.jwst_id || image.id,
              image.url || image.image_url || image.location || '',
              image.thumbnail || image.thumbnail_url || '',
              image.title || image.name || image.id || '',
              image.caption || image.description || '',
              image.instrument || 'UNKNOWN',
              image.program_id || image.program || '',
              image.suffix || ''
            ]
          );
          
          if (rows.length > 0) {
            savedCount++;
          }
        } catch (err) {
          console.warn('Failed to cache image:', image.jwst_id || image.id, err.message);
        }
      }
      
      await client.query('COMMIT');
      return { success: true, saved: savedCount };
      
    } catch (error) {
      await client.query('ROLLBACK');
      console.error('Error caching JWST images:', error);
      return { success: false, error: error.message, saved: savedCount };
      
    } finally {
      client.release();
    }
  }

  async getCachedImages(filters = {}) {
    const { 
      limit = 24, 
      offset = 0, 
      page = 1,
      instrument, 
      program, 
      suffix,
      source 
    } = filters;
    
    const effectiveLimit = parseInt(limit);
    const effectiveOffset = offset || (page - 1) * effectiveLimit;

    try {
      let query = `
        SELECT 
          id, jwst_id, url, thumbnail, title, caption,
          instrument, program_id, suffix, created_at, updated_at
        FROM jwst_images 
        WHERE 1=1
      `;
      
      let countQuery = `SELECT COUNT(*) FROM jwst_images WHERE 1=1`;
      const params = [];
      const countParams = [];
      let paramIndex = 1;
      let countParamIndex = 1;

      if (instrument && instrument.trim() !== '') {
        query += ` AND instrument = $${paramIndex}`;
        countQuery += ` AND instrument = $${countParamIndex}`;
        params.push(instrument.trim());
        countParams.push(instrument.trim());
        paramIndex++;
        countParamIndex++;
      }

      if (program && program.trim() !== '') {
        query += ` AND program_id = $${paramIndex}`;
        countQuery += ` AND program_id = $${countParamIndex}`;
        params.push(program.trim());
        countParams.push(program.trim());
        paramIndex++;
        countParamIndex++;
      }

      if (suffix && suffix.trim() !== '') {
        query += ` AND suffix LIKE $${paramIndex}`;
        countQuery += ` AND suffix LIKE $${countParamIndex}`;
        params.push(`%${suffix.trim()}%`);
        countParams.push(`%${suffix.trim()}%`);
        paramIndex++;
        countParamIndex++;
      }

      if (source === 'jpg') {
        query += ` AND (url ILIKE '%.jpg' OR url ILIKE '%.jpeg' OR suffix ILIKE '%jpg%')`;
        countQuery += ` AND (url ILIKE '%.jpg' OR url ILIKE '%.jpeg' OR suffix ILIKE '%jpg%')`;
      }

      const countResult = await this.pool.query(countQuery, countParams);
      const total = parseInt(countResult.rows[0].count) || 0;

      query += ` ORDER BY created_at DESC LIMIT $${paramIndex} OFFSET $${paramIndex + 1}`;
      params.push(effectiveLimit, effectiveOffset);

      const { rows } = await this.pool.query(query, params);
      
      return {
        items: rows,
        total: total,
        page: page,
        limit: effectiveLimit,
        offset: effectiveOffset,
        hasMore: (effectiveOffset + effectiveLimit) < total
      };
      
    } catch (error) {
      console.error('Error fetching cached JWST images:', error.message);
      return { 
        items: [], 
        total: 0, 
        page: page, 
        limit: effectiveLimit, 
        offset: effectiveOffset,
        hasMore: false 
      };
    }
  }

  async getImageById(id) {
    try {
      const { rows } = await this.pool.query(
        `SELECT * FROM jwst_images WHERE id = $1 OR jwst_id = $1 LIMIT 1`,
        [id]
      );
      return rows[0] || null;
    } catch (error) {
      console.error('Error fetching JWST image by ID:', error.message);
      return null;
    }
  }

  async getInstruments() {
    try {
      const { rows } = await this.pool.query(
        `SELECT DISTINCT instrument FROM jwst_images 
         WHERE instrument IS NOT NULL AND instrument != '' 
         ORDER BY instrument`
      );
      return rows.map(row => row.instrument);
    } catch (error) {
      console.error('Error fetching instruments:', error.message);
      return [];
    }
  }

  async getInstrumentStats() {
    try {
      const { rows } = await this.pool.query(
        `SELECT instrument, COUNT(*) as count 
         FROM jwst_images 
         WHERE instrument IS NOT NULL AND instrument != ''
         GROUP BY instrument 
         ORDER BY count DESC, instrument`
      );
      
      const stats = {};
      rows.forEach(row => {
        stats[row.instrument] = parseInt(row.count) || 0;
      });
      
      return stats;
    } catch (error) {
      console.error('Error fetching instrument stats:', error.message);
      return {};
    }
  }

  async getPrograms() {
    try {
      const { rows } = await this.pool.query(
        `SELECT DISTINCT program_id FROM jwst_images 
         WHERE program_id IS NOT NULL AND program_id != '' 
         ORDER BY program_id`
      );
      return rows.map(row => row.program_id);
    } catch (error) {
      console.error('Error fetching programs:', error.message);
      return [];
    }
  }

  async bulkInsertImages(images) {
    if (!images || images.length === 0) {
      return 0;
    }
    
    const client = await this.pool.connect();
    let insertedCount = 0;
    
    try {
      await client.query('BEGIN');
      
      for (const image of images) {
        const jwstId = image.jwst_id || image.id;
        if (!jwstId) continue;
        
        try {
          await client.query(
            `INSERT INTO jwst_images 
             (jwst_id, url, thumbnail, title, caption, instrument, program_id, suffix) 
             VALUES ($1, $2, $3, $4, $5, $6, $7, $8)
             ON CONFLICT (jwst_id) DO UPDATE SET
               url = EXCLUDED.url,
               thumbnail = EXCLUDED.thumbnail,
               title = EXCLUDED.title,
               caption = EXCLUDED.caption,
               instrument = EXCLUDED.instrument,
               program_id = EXCLUDED.program_id,
               suffix = EXCLUDED.suffix,
               updated_at = CURRENT_TIMESTAMP`,
            [
              jwstId,
              image.url || image.location || '',
              image.thumbnail || '',
              image.title || image.name || jwstId,
              image.caption || image.description || '',
              image.instrument || 'UNKNOWN',
              image.program_id || image.program || '',
              image.suffix || ''
            ]
          );
          
          insertedCount++;
        } catch (err) {
          if (err.code !== '23505') {
            console.warn(`Failed to insert image ${jwstId}:`, err.message);
          }
        }
      }
      
      await client.query('COMMIT');
      console.log(`Bulk insert: ${insertedCount} images processed`);
      return insertedCount;
      
    } catch (error) {
      await client.query('ROLLBACK');
      console.error('Error in bulk insert transaction:', error.message);
      throw error;
      
    } finally {
      client.release();
    }
  }

  async clearAllImages() {
    try {
      await this.pool.query('TRUNCATE TABLE jwst_images RESTART IDENTITY');
      console.log('JWST images table cleared');
      return { success: true };
    } catch (error) {
      console.error('Error clearing images table:', error.message);
      return { success: false, error: error.message };
    }
  }

  async getDatabaseInfo() {
    try {
      const countResult = await this.pool.query('SELECT COUNT(*) as total FROM jwst_images');
      const instrumentsResult = await this.pool.query(
        'SELECT COUNT(DISTINCT instrument) as instruments FROM jwst_images'
      );
      const programsResult = await this.pool.query(
        'SELECT COUNT(DISTINCT program_id) as programs FROM jwst_images'
      );
      const latestResult = await this.pool.query(
        'SELECT MAX(created_at) as latest FROM jwst_images'
      );
      
      return {
        total: parseInt(countResult.rows[0].total) || 0,
        instruments: parseInt(instrumentsResult.rows[0].instruments) || 0,
        programs: parseInt(programsResult.rows[0].programs) || 0,
        latest: latestResult.rows[0].latest || null
      };
    } catch (error) {
      console.error('Error getting database info:', error.message);
      return { total: 0, instruments: 0, programs: 0, latest: null };
    }
  }
}

module.exports = JwstRepository;