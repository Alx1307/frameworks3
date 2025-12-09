pub mod health;
pub mod iss;
pub mod osdr;
pub mod space_cache;

pub type ApiResult<T> = Result<T, (axum::http::StatusCode, String)>;