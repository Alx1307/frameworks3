use axum::{
    Router,
    routing::get,
};

use crate::handlers;
use crate::state::AppState;

pub fn create_api_routes() -> Router<AppState> {
    Router::new()
        .route("/health", get(handlers::health::health_check))
        .route("/last", get(handlers::iss::last_iss))
        .route("/fetch", get(handlers::iss::trigger_iss))
        .route("/iss/trend", get(handlers::iss::iss_trend))
        .route("/osdr/sync", get(handlers::osdr::osdr_sync))
        .route("/osdr/list", get(handlers::osdr::osdr_list))
        .route("/space/:src/latest", get(handlers::space_cache::space_latest))
        .route("/space/refresh", get(handlers::space_cache::space_refresh))
        .route("/space/summary", get(handlers::space_cache::space_summary))
}