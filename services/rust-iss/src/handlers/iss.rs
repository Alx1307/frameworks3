use axum::{
    extract::State,
    http::StatusCode,
    Json,
};
use chrono::{DateTime, Utc};
use serde::Serialize;
use serde_json::Value;

use crate::state::AppState;

#[derive(Serialize)]
pub struct Trend {
    pub movement: bool,
    pub delta_km: f64,
    pub dt_sec: f64,
    pub velocity_kmh: Option<f64>,
    pub from_time: Option<DateTime<Utc>>,
    pub to_time: Option<DateTime<Utc>>,
    pub from_lat: Option<f64>,
    pub from_lon: Option<f64>,
    pub to_lat: Option<f64>,
    pub to_lon: Option<f64>,
}

pub async fn last_iss(
    State(_st): State<AppState>
) -> Result<Json<Value>, (StatusCode, String)> {
    Ok(Json(serde_json::json!({"message": "TODO: implement last_iss"})))
}

pub async fn trigger_iss(
    State(_st): State<AppState>
) -> Result<Json<Value>, (StatusCode, String)> {
    Ok(Json(serde_json::json!({"message": "TODO: implement trigger_iss"})))
}

pub async fn iss_trend(
    State(_st): State<AppState>
) -> Result<Json<Trend>, (StatusCode, String)> {
    Ok(Json(Trend {
        movement: false,
        delta_km: 0.0,
        dt_sec: 0.0,
        velocity_kmh: None,
        from_time: None,
        to_time: None,
        from_lat: None,
        from_lon: None,
        to_lat: None,
        to_lon: None,
    }))
}