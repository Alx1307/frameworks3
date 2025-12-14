class CmsHandler {
    constructor(cmsService) {
      this.cmsService = cmsService;
    }
  
    async getAllBlocks(req, res, next) {
      try {
        const blocks = await this.cmsService.getAllBlocks();
        res.json({
          success: true,
          data: blocks,
          meta: {
            count: blocks.length,
            timestamp: new Date().toISOString()
          }
        });
      } catch (error) {
        next(error);
      }
    }
  
    async getBlockBySlug(req, res, next) {
      try {
        const { slug } = req.params;
        const block = await this.cmsService.getBlockBySlug(slug);
        
        if (!block) {
          return res.status(404).json({
            success: false,
            error: {
              message: `CMS block with slug "${slug}" not found`,
              code: 'NOT_FOUND'
            }
          });
        }
  
        res.json({
          success: true,
          data: block
        });
      } catch (error) {
        next(error);
      }
    }
  
    async createBlock(req, res, next) {
      try {
        const blockData = req.body;
        const block = await this.cmsService.createBlock(blockData);
        
        res.status(201).json({
          success: true,
          data: block,
          message: 'CMS block created successfully'
        });
      } catch (error) {
        next(error);
      }
    }
  
    async updateBlock(req, res, next) {
      try {
        const { id } = req.params;
        const blockData = req.body;
        
        const block = await this.cmsService.updateBlock(id, blockData);
        
        res.json({
          success: true,
          data: block,
          message: 'CMS block updated successfully'
        });
      } catch (error) {
        next(error);
      }
    }
  
    async deleteBlock(req, res, next) {
      try {
        const { id } = req.params;
        const result = await this.cmsService.deleteBlock(id);
        
        res.json({
          success: true,
          ...result
        });
      } catch (error) {
        next(error);
      }
    }
  
    async searchBlocks(req, res, next) {
      try {
        const { q } = req.query;
        
        if (!q) {
          return res.status(400).json({
            success: false,
            error: {
              message: 'Search query parameter "q" is required',
              code: 'VALIDATION_ERROR'
            }
          });
        }
  
        const blocks = await this.cmsService.searchBlocks(q);
        
        res.json({
          success: true,
          data: blocks,
          meta: {
            count: blocks.length,
            query: q,
            timestamp: new Date().toISOString()
          }
        });
      } catch (error) {
        next(error);
      }
    }
  }
  
  module.exports = CmsHandler;