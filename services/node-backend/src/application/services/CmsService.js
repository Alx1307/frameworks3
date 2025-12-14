const { sanitizeHtml } = require('../utils/htmlSanitizer');

class CmsService {
  constructor(cmsRepository, cacheRepository) {
    this.cmsRepository = cmsRepository;
    this.cacheRepository = cacheRepository;
  }

  async getAllBlocks() {
    try {
      const cacheKey = 'cms:all_blocks';
      const cached = await this.cacheRepository.get(cacheKey);
      
      if (cached) {
        return JSON.parse(cached);
      }

      const blocks = await this.cmsRepository.findAll();
      await this.cacheRepository.set(cacheKey, JSON.stringify(blocks), 300);
      return blocks;
    } catch (error) {
      console.error('CmsService.getAllBlocks error:', error);
      throw new Error('Failed to fetch CMS blocks');
    }
  }

  async getBlockBySlug(slug) {
    try {
      const cacheKey = `cms:block:${slug}`;
      const cached = await this.cacheRepository.get(cacheKey);
      
      if (cached) {
        return JSON.parse(cached);
      }

      const block = await this.cmsRepository.findBySlug(slug);
      if (!block) {
        return null;
      }

      block.content = sanitizeHtml(block.content);
      
      await this.cacheRepository.set(cacheKey, JSON.stringify(block), 600);
      return block;
    } catch (error) {
      console.error('CmsService.getBlockBySlug error:', error);
      throw new Error(`Failed to fetch block with slug "${slug}"`);
    }
  }

  async createBlock(blockData) {
    try {
      this._validateBlockData(blockData);
      
      if (blockData.content) {
        blockData.content = sanitizeHtml(blockData.content);
      }

      const block = await this.cmsRepository.create(blockData);
      
      await this._invalidateCache();
      
      return block;
    } catch (error) {
      console.error('CmsService.createBlock error:', error);
      throw error;
    }
  }

  async updateBlock(id, blockData) {
    try {
      if (blockData.content) {
        blockData.content = sanitizeHtml(blockData.content);
      }

      const block = await this.cmsRepository.update(id, blockData);
      if (!block) {
        throw new Error(`Block with id ${id} not found`);
      }

      await this._invalidateCache();
      await this.cacheRepository.delete(`cms:block:${block.slug}`);
      
      return block;
    } catch (error) {
      console.error('CmsService.updateBlock error:', error);
      throw error;
    }
  }

  async deleteBlock(id) {
    try {
      const block = await this.cmsRepository.delete(id);
      if (!block) {
        throw new Error(`Block with id ${id} not found`);
      }

      await this._invalidateCache();
      
      return { success: true, message: 'Block deleted successfully' };
    } catch (error) {
      console.error('CmsService.deleteBlock error:', error);
      throw error;
    }
  }

  async searchBlocks(query) {
    try {
      if (!query || query.trim().length < 2) {
        return [];
      }

      return await this.cmsRepository.search(query.trim());
    } catch (error) {
      console.error('CmsService.searchBlocks error:', error);
      throw new Error('Failed to search blocks');
    }
  }

  _validateBlockData(blockData) {
    const { slug, title, content } = blockData;
    
    if (!slug || !slug.trim()) {
      throw new Error('Slug is required');
    }
    
    if (!title || !title.trim()) {
      throw new Error('Title is required');
    }
    
    if (!content || !content.trim()) {
      throw new Error('Content is required');
    }

    const slugRegex = /^[a-z0-9-]+$/;
    if (!slugRegex.test(slug)) {
      throw new Error('Slug can only contain lowercase letters, numbers, and hyphens');
    }

    if (slug.length > 255) {
      throw new Error('Slug is too long (max 255 characters)');
    }

    if (title.length > 255) {
      throw new Error('Title is too long (max 255 characters)');
    }
  }

  async _invalidateCache() {
    try {
      const keys = await this.cacheRepository.keys('cms:*');
      if (keys.length > 0) {
        await this.cacheRepository.deleteAll(keys);
      }
    } catch (error) {
      console.warn('Failed to invalidate cache:', error.message);
    }
  }
}

module.exports = CmsService;