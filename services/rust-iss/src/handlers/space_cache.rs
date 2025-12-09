use axum::{
    extract::{Path, Query, State},
    http::StatusCode,
    Json,
};
use std::collections::HashMap;
use serde_json::Value;

use crate::state::AppState;

pub async fn space_latest(
    Path(_src): Path<String>,
    State(_st): State<AppState>
) -> Result<Json<Value>, (StatusCode, String)> {
    Ok(Json(serde_json::json!({"message": "TODO: implement space_latest"})))
}

pub async fn space_refresh(
    Query(_q): Query<HashMap<String, String>>,
    State(_st): State<AppState>
) -> Result<Json<Value>, (StatusCode, String)> {
    Ok(Json(serde_json::json!({"message": "TODO: implement space_refresh"})))
}

pub async fn space_summary(
    State(_st): State<AppState>
) -> Result<Json<Value>, (StatusCode, String)> {
    Ok(Json(serde_json::json!({"message": "TODO: implement space_summary"})))
}