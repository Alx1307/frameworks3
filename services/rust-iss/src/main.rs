mod config;
mod domain;

use std::{collections::HashMap, time::Duration};

use axum::{
    extract::{Path, Query, State},
    http::StatusCode,
    routing::get,
    Json, Router,
};
use chrono::{DateTime, NaiveDateTime, TimeZone, Utc};
use serde::Serialize;
use serde_json::Value;
use sqlx::{postgres::PgPoolOptions, PgPool, Row};
use tracing::{error, info};
use tracing_subscriber::{EnvFilter, FmtSubscriber};

use config::Config;
use domain::iss::{IssTrend, haversine_km, extract_number};
use domain::osdr::{OsdrItem, OsdrSyncResult};
use domain::space_cache::{SpaceCacheLatest, SpaceCacheRefreshResult, SpaceSummary, CacheSource};

#[derive(Serialize)]
struct Health { status: &'static str, now: DateTime<Utc> }

#[derive(Clone)]
struct AppState {
    pool: PgPool,
    config: Config,
}

#[tokio::main]
async fn main() -> anyhow::Result<()> {
    let subscriber = FmtSubscriber::builder()
        .with_env_filter(EnvFilter::from_default_env())
        .finish();
    let _ = tracing::subscriber::set_global_default(subscriber);

    let config = Config::from_env()?;

    let pool = PgPoolOptions::new().max_connections(5).connect(&config.database_url).await?;

    let state = AppState {
        pool: pool.clone(),
        config: config.clone(),
    };

    // фон OSDR
    {
        let st = state.clone();
        tokio::spawn(async move {
            loop {
                if let Err(e) = fetch_and_store_osdr(&st).await { error!("osdr err {e:?}") }
                tokio::time::sleep(Duration::from_secs(st.config.fetch_every_seconds)).await;
            }
        });
    }
    // фон ISS
    {
        let st = state.clone();
        tokio::spawn(async move {
            loop {
                if let Err(e) = fetch_and_store_iss(&st.pool, &st.config.where_iss_url).await { error!("iss err {e:?}") }
                tokio::time::sleep(Duration::from_secs(st.config.iss_every_seconds)).await;
            }
        });
    }
    // фон APOD
    {
        let st = state.clone();
        tokio::spawn(async move {
            loop {
                if let Err(e) = fetch_apod(&st).await { error!("apod err {e:?}") }
                tokio::time::sleep(Duration::from_secs(st.config.apod_every_seconds)).await;
            }
        });
    }
    // фон NeoWs
    {
        let st = state.clone();
        tokio::spawn(async move {
            loop {
                if let Err(e) = fetch_neo_feed(&st).await { error!("neo err {e:?}") }
                tokio::time::sleep(Duration::from_secs(st.config.neo_every_seconds)).await;
            }
        });
    }
    // фон DONKI
    {
        let st = state.clone();
        tokio::spawn(async move {
            loop {
                if let Err(e) = fetch_donki(&st).await { error!("donki err {e:?}") }
                tokio::time::sleep(Duration::from_secs(st.config.donki_every_seconds)).await;
            }
        });
    }
    // фон SpaceX
    {
        let st = state.clone();
        tokio::spawn(async move {
            loop {
                if let Err(e) = fetch_spacex_next(&st).await { error!("spacex err {e:?}") }
                tokio::time::sleep(Duration::from_secs(st.config.spacex_every_seconds)).await;
            }
        });
    }

    let app = Router::new()
        // общее
        .route("/health", get(|| async { Json(Health { status: "ok", now: Utc::now() }) }))
        .with_state(state.clone())
        // ISS
        .route("/last", get(last_iss))
        .route("/fetch", get(trigger_iss))
        .route("/iss/trend", get(iss_trend))
        // OSDR
        .route("/osdr/sync", get(osdr_sync))
        .route("/osdr/list", get(osdr_list))
        // Space cache
        .route("/space/:src/latest", get(space_latest))
        .route("/space/refresh", get(space_refresh))
        .route("/space/summary", get(space_summary))
        .with_state(state);

    let listener = tokio::net::TcpListener::bind(("0.0.0.0", 3000)).await?;
    info!("rust_iss listening on 0.0.0.0:3000");
    axum::serve(listener, app.into_make_service()).await?;
    Ok(())
}

/* ---------- ISS ---------- */
async fn last_iss(State(st): State<AppState>)
-> Result<Json<Value>, (StatusCode, String)> {
    let row_opt = sqlx::query(
        "SELECT id, fetched_at, source_url, payload
         FROM iss_fetch_log
         ORDER BY id DESC LIMIT 1"
    ).fetch_optional(&st.pool).await
     .map_err(|e| (StatusCode::INTERNAL_SERVER_ERROR, e.to_string()))?;

    if let Some(row) = row_opt {
        let id: i64 = row.get("id");
        let fetched_at: DateTime<Utc> = row.get::<DateTime<Utc>, _>("fetched_at");
        let source_url: String = row.get("source_url");
        let payload: Value = row.try_get("payload").unwrap_or(serde_json::json!({}));
        return Ok(Json(serde_json::json!({
            "id": id, "fetched_at": fetched_at, "source_url": source_url, "payload": payload
        })));
    }
    Ok(Json(serde_json::json!({"message":"no data"})))
}

async fn trigger_iss(State(st): State<AppState>)
-> Result<Json<Value>, (StatusCode, String)> {
    fetch_and_store_iss(&st.pool, &st.config.where_iss_url).await
        .map_err(|e| (StatusCode::INTERNAL_SERVER_ERROR, e.to_string()))?;
    last_iss(State(st)).await
}

async fn iss_trend(State(st): State<AppState>)
-> Result<Json<IssTrend>, (StatusCode, String)> {
    let rows = sqlx::query("SELECT fetched_at, payload FROM iss_fetch_log ORDER BY id DESC LIMIT 2")
        .fetch_all(&st.pool).await
        .map_err(|e| (StatusCode::INTERNAL_SERVER_ERROR, e.to_string()))?;

    if rows.len() < 2 {
        return Ok(Json(IssTrend::empty()));
    }

    let t2: DateTime<Utc> = rows[0].get("fetched_at");
    let t1: DateTime<Utc> = rows[1].get("fetched_at");
    let p2: Value = rows[0].get("payload");
    let p1: Value = rows[1].get("payload");

    let lat1 = extract_number(&p1["latitude"]);
    let lon1 = extract_number(&p1["longitude"]);
    let lat2 = extract_number(&p2["latitude"]);
    let lon2 = extract_number(&p2["longitude"]);
    let v2 = extract_number(&p2["velocity"]);

    let mut delta_km = 0.0;
    let mut movement = false;
    if let (Some(a1), Some(o1), Some(a2), Some(o2)) = (lat1, lon1, lat2, lon2) {
        delta_km = haversine_km(a1, o1, a2, o2);
        movement = delta_km > 0.1;
    }
    let dt_sec = (t2 - t1).num_milliseconds() as f64 / 1000.0;

    Ok(Json(IssTrend {
        movement,
        delta_km,
        dt_sec,
        velocity_kmh: v2,
        from_time: Some(t1),
        to_time: Some(t2),
        from_lat: lat1, from_lon: lon1, to_lat: lat2, to_lon: lon2,
    }))
}

/* ---------- OSDR ---------- */
async fn osdr_sync(State(st): State<AppState>)
-> Result<Json<OsdrSyncResult>, (StatusCode, String)> {
    let written = fetch_and_store_osdr(&st).await
        .map_err(|e| (StatusCode::INTERNAL_SERVER_ERROR, e.to_string()))?;
    Ok(Json(OsdrSyncResult { written }))
}

async fn osdr_list(State(st): State<AppState>)
-> Result<Json<Value>, (StatusCode, String)> {
    let limit =  std::env::var("OSDR_LIST_LIMIT").ok()
        .and_then(|s| s.parse::<i64>().ok()).unwrap_or(20);

    let rows = sqlx::query(
        "SELECT id, dataset_id, title, status, updated_at, inserted_at, raw
         FROM osdr_items
         ORDER BY inserted_at DESC
         LIMIT $1"
    ).bind(limit).fetch_all(&st.pool).await
     .map_err(|e| (StatusCode::INTERNAL_SERVER_ERROR, e.to_string()))?;

    let out: Vec<OsdrItem> = rows.into_iter().map(|r| {
        OsdrItem {
            id: r.get::<i64,_>("id"),
            dataset_id: r.get::<Option<String>,_>("dataset_id"),
            title: r.get::<Option<String>,_>("title"),
            status: r.get::<Option<String>,_>("status"),
            updated_at: r.get::<Option<DateTime<Utc>>,_>("updated_at"),
            inserted_at: r.get::<DateTime<Utc>, _>("inserted_at"),
            raw: r.get::<Value,_>("raw"),
        }
    }).collect();

    Ok(Json(serde_json::json!({ "items": out })))
}

/* ---------- Универсальная витрина space_cache ---------- */
async fn space_latest(Path(src): Path<String>, State(st): State<AppState>)
-> Result<Json<SpaceCacheLatest>, (StatusCode, String)> {
    let row = sqlx::query(
        "SELECT fetched_at, payload FROM space_cache
         WHERE source = $1 ORDER BY id DESC LIMIT 1"
    ).bind(&src).fetch_optional(&st.pool).await
     .map_err(|e| (StatusCode::INTERNAL_SERVER_ERROR, e.to_string()))?;

    if let Some(r) = row {
        let fetched_at: DateTime<Utc> = r.get("fetched_at");
        let payload: Value = r.get("payload");
        return Ok(Json(SpaceCacheLatest {
            source: src,
            fetched_at,
            payload,
        }));
    }
    
    Err((StatusCode::NOT_FOUND, format!("No data for source: {}", src)))
}

async fn space_refresh(Query(q): Query<HashMap<String,String>>, State(st): State<AppState>)
-> Result<Json<SpaceCacheRefreshResult>, (StatusCode, String)> {
    let list = q.get("src").cloned().unwrap_or_else(|| "apod,neo,flr,cme,spacex".to_string());
    let mut done = Vec::new();
    
    for s in list.split(',').map(|x| x.trim().to_lowercase()) {
        if let Some(source) = CacheSource::from_str(&s) {
            match source {
                CacheSource::Apod => { let _ = fetch_apod(&st).await; done.push("apod".to_string()); }
                CacheSource::Neo => { let _ = fetch_neo_feed(&st).await; done.push("neo".to_string()); }
                CacheSource::Flr => { let _ = fetch_donki_flr(&st).await; done.push("flr".to_string()); }
                CacheSource::Cme => { let _ = fetch_donki_cme(&st).await; done.push("cme".to_string()); }
                CacheSource::SpaceX => { let _ = fetch_spacex_next(&st).await; done.push("spacex".to_string()); }
            }
        }
    }
    
    Ok(Json(SpaceCacheRefreshResult { refreshed: done }))
}

async fn latest_from_cache(pool: &PgPool, src: &str) -> Value {
    sqlx::query("SELECT fetched_at, payload FROM space_cache WHERE source=$1 ORDER BY id DESC LIMIT 1")
        .bind(src)
        .fetch_optional(pool).await.ok().flatten()
        .map(|r| serde_json::json!({"at": r.get::<DateTime<Utc>,_>("fetched_at"), "payload": r.get::<Value,_>("payload")}))
        .unwrap_or(serde_json::json!({}))
}

async fn space_summary(State(st): State<AppState>)
-> Result<Json<SpaceSummary>, (StatusCode, String)> {
    let apod = latest_from_cache(&st.pool, "apod").await;
    let neo = latest_from_cache(&st.pool, "neo").await;
    let flr = latest_from_cache(&st.pool, "flr").await;
    let cme = latest_from_cache(&st.pool, "cme").await;
    let spacex = latest_from_cache(&st.pool, "spacex").await;

    let iss_last = sqlx::query("SELECT fetched_at,payload FROM iss_fetch_log ORDER BY id DESC LIMIT 1")
        .fetch_optional(&st.pool).await.ok().flatten()
        .map(|r| serde_json::json!({"at": r.get::<DateTime<Utc>,_>("fetched_at"), "payload": r.get::<Value,_>("payload")}))
        .unwrap_or(serde_json::json!({}));

    let osdr_count: i64 = sqlx::query("SELECT count(*) AS c FROM osdr_items")
        .fetch_one(&st.pool).await.map(|r| r.get::<i64,_>("c")).unwrap_or(0);

    Ok(Json(SpaceSummary {
        apod,
        neo,
        flr,
        cme,
        spacex,
        iss: iss_last,
        osdr_count,
    }))
}

/* ---------- Фетчеры и запись ---------- */

async fn write_cache(pool: &PgPool, source: &str, payload: Value) -> anyhow::Result<()> {
    sqlx::query("INSERT INTO space_cache(source, payload) VALUES ($1,$2)")
        .bind(source).bind(payload).execute(pool).await?;
    Ok(())
}

// APOD - обновляем использование config
async fn fetch_apod(st: &AppState) -> anyhow::Result<()> {
    let url = "https://api.nasa.gov/planetary/apod";
    let client = reqwest::Client::builder().timeout(Duration::from_secs(30)).build()?;
    let mut req = client.get(url).query(&[("thumbs","true")]);
    if !st.config.nasa_api_key.is_empty() { req = req.query(&[("api_key",&st.config.nasa_api_key)]); }
    let json: Value = req.send().await?.json().await?;
    write_cache(&st.pool, "apod", json).await
}

// NeoWs - обновляем использование config
async fn fetch_neo_feed(st: &AppState) -> anyhow::Result<()> {
    let today = Utc::now().date_naive();
    let start = today - chrono::Days::new(2);
    let url = "https://api.nasa.gov/neo/rest/v1/feed";
    let client = reqwest::Client::builder().timeout(Duration::from_secs(30)).build()?;
    let mut req = client.get(url).query(&[
        ("start_date", start.to_string()),
        ("end_date", today.to_string()),
    ]);
    if !st.config.nasa_api_key.is_empty() { req = req.query(&[("api_key",&st.config.nasa_api_key)]); }
    let json: Value = req.send().await?.json().await?;
    write_cache(&st.pool, "neo", json).await
}

// DONKI объединённая
async fn fetch_donki(st: &AppState) -> anyhow::Result<()> {
    let _ = fetch_donki_flr(st).await;
    let _ = fetch_donki_cme(st).await;
    Ok(())
}

async fn fetch_donki_flr(st: &AppState) -> anyhow::Result<()> {
    let (from,to) = last_days(5);
    let url = "https://api.nasa.gov/DONKI/FLR";
    let client = reqwest::Client::builder().timeout(Duration::from_secs(30)).build()?;
    let mut req = client.get(url).query(&[("startDate",from),("endDate",to)]);
    if !st.config.nasa_api_key.is_empty() { req = req.query(&[("api_key",&st.config.nasa_api_key)]); }
    let json: Value = req.send().await?.json().await?;
    write_cache(&st.pool, "flr", json).await
}

async fn fetch_donki_cme(st: &AppState) -> anyhow::Result<()> {
    let (from,to) = last_days(5);
    let url = "https://api.nasa.gov/DONKI/CME";
    let client = reqwest::Client::builder().timeout(Duration::from_secs(30)).build()?;
    let mut req = client.get(url).query(&[("startDate",from),("endDate",to)]);
    if !st.config.nasa_api_key.is_empty() { req = req.query(&[("api_key",&st.config.nasa_api_key)]); }
    let json: Value = req.send().await?.json().await?;
    write_cache(&st.pool, "cme", json).await
}

// SpaceX
async fn fetch_spacex_next(st: &AppState) -> anyhow::Result<()> {
    let url = "https://api.spacexdata.com/v4/launches/next";
    let client = reqwest::Client::builder().timeout(Duration::from_secs(30)).build()?;
    let json: Value = client.get(url).send().await?.json().await?;
    write_cache(&st.pool, "spacex", json).await
}

fn last_days(n: i64) -> (String,String) {
    let to = Utc::now().date_naive();
    let from = to - chrono::Days::new(n as u64);
    (from.to_string(), to.to_string())
}

/* ---------- Вспомогательное ---------- */
fn s_pick(v: &Value, keys: &[&str]) -> Option<String> {
    for k in keys {
        if let Some(x) = v.get(*k) {
            if let Some(s) = x.as_str() { if !s.is_empty() { return Some(s.to_string()); } }
            else if x.is_number() { return Some(x.to_string()); }
        }
    }
    None
}
fn t_pick(v: &Value, keys: &[&str]) -> Option<DateTime<Utc>> {
    for k in keys {
        if let Some(x) = v.get(*k) {
            if let Some(s) = x.as_str() {
                if let Ok(dt) = s.parse::<DateTime<Utc>>() { return Some(dt); }
                if let Ok(ndt) = NaiveDateTime::parse_from_str(s, "%Y-%m-%d %H:%M:%S") {
                    return Some(Utc.from_utc_datetime(&ndt));
                }
            } else if let Some(n) = x.as_i64() {
                return Some(Utc.timestamp_opt(n, 0).single().unwrap_or_else(Utc::now));
            }
        }
    }
    None
}

async fn fetch_and_store_iss(pool: &PgPool, url: &str) -> anyhow::Result<()> {
    let client = reqwest::Client::builder().timeout(Duration::from_secs(20)).build()?;
    let resp = client.get(url).send().await?;
    let json: Value = resp.json().await?;
    sqlx::query("INSERT INTO iss_fetch_log (source_url, payload) VALUES ($1, $2)")
        .bind(url).bind(json).execute(pool).await?;
    Ok(())
}

async fn fetch_and_store_osdr(st: &AppState) -> anyhow::Result<usize> {
    let client = reqwest::Client::builder().timeout(Duration::from_secs(30)).build()?;
    let resp = client.get(&st.config.nasa_api_url).send().await?; // Используем config
    if !resp.status().is_success() {
        anyhow::bail!("OSDR request status {}", resp.status());
    }
    let json: Value = resp.json().await?;
    let items = if let Some(a) = json.as_array() { a.clone() }
        else if let Some(v) = json.get("items").and_then(|x| x.as_array()) { v.clone() }
        else if let Some(v) = json.get("results").and_then(|x| x.as_array()) { v.clone() }
        else { vec![json.clone()] };

    let mut written = 0usize;
    for item in items {
        let id = s_pick(&item, &["dataset_id","id","uuid","studyId","accession","osdr_id"]);
        let title = s_pick(&item, &["title","name","label"]);
        let status = s_pick(&item, &["status","state","lifecycle"]);
        let updated = t_pick(&item, &["updated","updated_at","modified","lastUpdated","timestamp"]);
        if let Some(ds) = id.clone() {
            sqlx::query(
                "INSERT INTO osdr_items(dataset_id, title, status, updated_at, raw)
                 VALUES($1,$2,$3,$4,$5)
                 ON CONFLICT (dataset_id) DO UPDATE
                 SET title=EXCLUDED.title, status=EXCLUDED.status,
                     updated_at=EXCLUDED.updated_at, raw=EXCLUDED.raw"
            ).bind(ds).bind(title).bind(status).bind(updated).bind(item).execute(&st.pool).await?;
        } else {
            sqlx::query(
                "INSERT INTO osdr_items(dataset_id, title, status, updated_at, raw)
                 VALUES($1,$2,$3,$4,$5)"
            ).bind::<Option<String>>(None).bind(title).bind(status).bind(updated).bind(item).execute(&st.pool).await?;
        }
        written += 1;
    }
    Ok(written)
}