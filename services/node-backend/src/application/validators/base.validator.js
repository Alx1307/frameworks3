class BaseValidator {
    constructor() {
      this.errors = [];
      this.validatedData = null;
    }
  
    validate(data) {
      this.errors = [];
      this.validatedData = null;
      return this;
    }
  
    isValid() {
      return this.errors.length === 0;
    }
  
    getErrors() {
      return this.errors;
    }
  
    getValidatedData() {
      return this.validatedData;
    }
  
    addError(field, message, code = 'VALIDATION_ERROR') {
      this.errors.push({
        field,
        message,
        code,
        timestamp: new Date().toISOString()
      });
    }
  
    isRequired(value, field) {
      if (value === undefined || value === null || value === '') {
        this.addError(field, `${field} is required`);
        return false;
      }
      return true;
    }
  
    isString(value, field, minLength = 1, maxLength = 255) {
      if (typeof value !== 'string') {
        this.addError(field, `${field} must be a string`);
        return false;
      }
      if (value.length < minLength) {
        this.addError(field, `${field} must be at least ${minLength} characters`);
        return false;
      }
      if (value.length > maxLength) {
        this.addError(field, `${field} must not exceed ${maxLength} characters`);
        return false;
      }
      return true;
    }
  
    isNumber(value, field, min = null, max = null) {
      if (typeof value !== 'number' || isNaN(value)) {
        this.addError(field, `${field} must be a valid number`);
        return false;
      }
      if (min !== null && value < min) {
        this.addError(field, `${field} must be at least ${min}`);
        return false;
      }
      if (max !== null && value > max) {
        this.addError(field, `${field} must not exceed ${max}`);
        return false;
      }
      return true;
    }
  
    isInRange(value, field, min, max) {
      return this.isNumber(value, field, min, max);
    }
  
    isTimestamp(value, field) {
      if (!value || isNaN(Date.parse(value))) {
        this.addError(field, `${field} must be a valid ISO timestamp`);
        return false;
      }
      return true;
    }
  
    isBoolean(value, field) {
      if (typeof value !== 'boolean') {
        this.addError(field, `${field} must be a boolean`);
        return false;
      }
      return true;
    }
  
    isUrl(value, field) {
      try {
        new URL(value);
        return true;
      } catch {
        this.addError(field, `${field} must be a valid URL`);
        return false;
      }
    }
  
    isArray(value, field, minLength = 0, maxLength = null) {
      if (!Array.isArray(value)) {
        this.addError(field, `${field} must be an array`);
        return false;
      }
      if (value.length < minLength) {
        this.addError(field, `${field} must contain at least ${minLength} items`);
        return false;
      }
      if (maxLength !== null && value.length > maxLength) {
        this.addError(field, `${field} must not contain more than ${maxLength} items`);
        return false;
      }
      return true;
    }
  
    sanitizeString(value) {
      if (typeof value === 'string') {
        return value.trim();
      }
      return value;
    }
  
    sanitizeNumber(value) {
      if (typeof value === 'string') {
        const num = parseFloat(value);
        return isNaN(num) ? value : num;
      }
      return value;
    }
  }
  
  module.exports = BaseValidator;