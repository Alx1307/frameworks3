use std::time::Duration;

#[derive(Clone)]
pub struct Config {
    pub database_url: String,
    pub nasa_api_url: String,
    pub nasa_api_key: String,
    pub where_iss_url: String,
    pub fetch_every_seconds: u64,
    pub iss_every_seconds: u64,
    pub apod_every_seconds: u64,
    pub neo_every_seconds: u64,
    pub donki_every_seconds: u64,
    pub spacex_every_seconds: u64,
}

impl Config {
    pub fn from_env() -> anyhow::Result<Self> {
        dotenvy::dotenv().ok();
        
        Ok(Self {
            database_url: std::env::var("DATABASE_URL")
                .expect("DATABASE_URL is required"),
            nasa_api_url: std::env::var("NASA_API_URL")
                .unwrap_or_else(|_| "https://visualization.osdr.nasa.gov/biodata/api/v2/datasets/?format=json".to_string()),
            nasa_api_key: std::env::var("NASA_API_KEY").unwrap_or_default(),
            where_iss_url: std::env::var("WHERE_ISS_URL")
                .unwrap_or_else(|_| "https://api.wheretheiss.at/v1/satellites/25544".to_string()),
            fetch_every_seconds: env_u64("FETCH_EVERY_SECONDS", 600),
            iss_every_seconds: env_u64("ISS_EVERY_SECONDS", 120),
            apod_every_seconds: env_u64("APOD_EVERY_SECONDS", 43200),
            neo_every_seconds: env_u64("NEO_EVERY_SECONDS", 7200),
            donki_every_seconds: env_u64("DONKI_EVERY_SECONDS", 3600),
            spacex_every_seconds: env_u64("SPACEX_EVERY_SECONDS", 3600),
        })
    }
}

fn env_u64(k: &str, d: u64) -> u64 {
    std::env::var(k).ok().and_then(|s| s.parse().ok()).unwrap_or(d)
}