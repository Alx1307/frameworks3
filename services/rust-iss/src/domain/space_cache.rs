use chrono::{DateTime, Utc};
use serde::{Deserialize, Serialize};
use serde_json::Value;

#[derive(Debug, Serialize, Deserialize)]
pub struct SpaceCacheEntry {
    pub source: String,
    pub fetched_at: DateTime<Utc>,
    pub payload: Value,
}

#[derive(Debug, Serialize)]
pub struct SpaceCacheLatest {
    pub source: String,
    pub fetched_at: DateTime<Utc>,
    pub payload: Value,
}

#[derive(Debug, Serialize)]
pub struct SpaceCacheRefreshResult {
    pub refreshed: Vec<String>,
}

#[derive(Debug, Serialize)]
pub struct SpaceSummary {
    pub apod: Value,
    pub neo: Value,
    pub flr: Value,
    pub cme: Value,
    pub spacex: Value,
    pub iss: Value,
    pub osdr_count: i64,
}

#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum CacheSource {
    Apod,
    Neo,
    Flr,
    Cme,
    SpaceX,
}

impl CacheSource {
    pub fn as_str(&self) -> &'static str {
        match self {
            CacheSource::Apod => "apod",
            CacheSource::Neo => "neo",
            CacheSource::Flr => "flr",
            CacheSource::Cme => "cme",
            CacheSource::SpaceX => "spacex",
        }
    }
    
    pub fn from_str(s: &str) -> Option<Self> {
        match s.to_lowercase().as_str() {
            "apod" => Some(CacheSource::Apod),
            "neo" => Some(CacheSource::Neo),
            "flr" => Some(CacheSource::Flr),
            "cme" => Some(CacheSource::Cme),
            "spacex" => Some(CacheSource::SpaceX),
            _ => None,
        }
    }
}