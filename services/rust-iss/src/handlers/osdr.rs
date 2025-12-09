use axum::{
    extract::State,
    http::StatusCode,
    Json,
};
use serde_json::Value;

use crate::state::AppState;

pub async fn osdr_sync(
    State(_st): State<AppState>
) -> Result<Json<Value>, (StatusCode, String)> {
    Ok(Json(serde_json::json!({"message": "TODO: implement osdr_sync"})))
}

pub async fn osdr_list(
    State(_st): State<AppState>
) -> Result<Json<Value>, (StatusCode, String)> {
    Ok(Json(serde_json::json!({"message": "TODO: implement osdr_list"})))
}