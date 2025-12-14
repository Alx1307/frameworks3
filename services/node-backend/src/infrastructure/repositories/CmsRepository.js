class CmsRepository {
    constructor(pool) {
      this.pool = pool;
    }
  
    async findAll() {
      try {
        const result = await this.pool.query(`
          SELECT id, slug, title, content, is_active, created_at, updated_at 
          FROM cms_blocks 
          WHERE is_active = TRUE 
          ORDER BY updated_at DESC
        `);
        return result.rows;
      } catch (error) {
        console.error('CmsRepository.findAll error:', error);
        throw error;
      }
    }
  
    async findBySlug(slug) {
      try {
        const result = await this.pool.query(`
          SELECT id, slug, title, content, is_active, created_at, updated_at 
          FROM cms_blocks 
          WHERE slug = $1 AND is_active = TRUE 
          LIMIT 1
        `, [slug]);
        return result.rows[0] || null;
      } catch (error) {
        console.error('CmsRepository.findBySlug error:', error);
        throw error;
      }
    }
  
    async findById(id) {
      try {
        const result = await this.pool.query(`
          SELECT id, slug, title, content, is_active, created_at, updated_at 
          FROM cms_blocks 
          WHERE id = $1 
          LIMIT 1
        `, [id]);
        return result.rows[0] || null;
      } catch (error) {
        console.error('CmsRepository.findById error:', error);
        throw error;
      }
    }
  
    async create(blockData) {
      const { slug, title, content, is_active = true } = blockData;
      try {
        const result = await this.pool.query(`
          INSERT INTO cms_blocks (slug, title, content, is_active)
          VALUES ($1, $2, $3, $4)
          RETURNING id, slug, title, content, is_active, created_at, updated_at
        `, [slug, title, content, is_active]);
        return result.rows[0];
      } catch (error) {
        if (error.code === '23505') {
          throw new Error(`Block with slug "${slug}" already exists`);
        }
        throw error;
      }
    }
  
    async update(id, blockData) {
      const { slug, title, content, is_active } = blockData;
      const updates = [];
      const values = [];
      let paramCount = 1;
  
      if (slug !== undefined) {
        updates.push(`slug = $${paramCount++}`);
        values.push(slug);
      }
      if (title !== undefined) {
        updates.push(`title = $${paramCount++}`);
        values.push(title);
      }
      if (content !== undefined) {
        updates.push(`content = $${paramCount++}`);
        values.push(content);
      }
      if (is_active !== undefined) {
        updates.push(`is_active = $${paramCount++}`);
        values.push(is_active);
      }
  
      if (updates.length === 0) {
        throw new Error('No fields to update');
      }
  
      values.push(id);
      updates.push(`updated_at = NOW()`);
  
      try {
        const result = await this.pool.query(`
          UPDATE cms_blocks 
          SET ${updates.join(', ')}
          WHERE id = $${paramCount}
          RETURNING id, slug, title, content, is_active, created_at, updated_at
        `, values);
        return result.rows[0] || null;
      } catch (error) {
        console.error('CmsRepository.update error:', error);
        throw error;
      }
    }
  
    async delete(id) {
      try {
        const result = await this.pool.query(`
          UPDATE cms_blocks 
          SET is_active = FALSE, updated_at = NOW()
          WHERE id = $1
          RETURNING id
        `, [id]);
        return result.rows[0];
      } catch (error) {
        console.error('CmsRepository.delete error:', error);
        throw error;
      }
    }
  
    async search(query) {
      try {
        const searchTerm = `%${query}%`;
        const result = await this.pool.query(`
          SELECT id, slug, title, content, is_active, created_at, updated_at
          FROM cms_blocks 
          WHERE (LOWER(title) LIKE LOWER($1) OR LOWER(slug) LIKE LOWER($1))
            AND is_active = TRUE
          ORDER BY updated_at DESC
        `, [searchTerm]);
        return result.rows;
      } catch (error) {
        console.error('CmsRepository.search error:', error);
        throw error;
      }
    }
  }
  
  module.exports = CmsRepository;