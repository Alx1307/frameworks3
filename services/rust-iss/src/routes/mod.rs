pub mod api;

use axum::Router;
use crate::state::AppState;

pub fn create_router(state: AppState) -> Router<AppState> {
    Router::new()
        .merge(api::create_api_routes())
        .with_state(state)
}