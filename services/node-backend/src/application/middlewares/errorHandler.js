const ApiError = require('../../shared/errors/ApiError');

module.exports = (err, req, res, next) => {
  console.error('Error:', err);

  if (err instanceof ApiError) {
    return res.status(err.statusCode).json({
      error: {
        message: err.message,
        details: err.details,
        timestamp: new Date().toISOString()
      }
    });
  }

  const statusCode = err.statusCode || 500;
  const message = err.message || 'Internal Server Error';
  const details = process.env.NODE_ENV === 'development' ? err.stack : undefined;

  res.status(statusCode).json({
    error: {
      message,
      details,
      timestamp: new Date().toISOString()
    }
  });
};