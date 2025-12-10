const Joi = require('joi');

class IssValidator {
  static positionSchema = Joi.object({
    latitude: Joi.number().min(-90).max(90).required(),
    longitude: Joi.number().min(-180).max(180).required(),
    altitude: Joi.number().min(0).optional(),
    velocity: Joi.number().min(0).optional(),
    visibility: Joi.string().optional(),
    timestamp: Joi.number().integer().positive().required()
  });

  static trendQuerySchema = Joi.object({
    hours: Joi.number().integer().min(1).max(24).default(1),
    limit: Joi.number().integer().min(1).max(1000).default(100)
  });

  static validatePosition(data) {
    return this.positionSchema.validate(data, { abortEarly: false });
  }

  static validateTrendQuery(query) {
    return this.trendQuerySchema.validate(query, { abortEarly: false });
  }
}

module.exports = IssValidator;