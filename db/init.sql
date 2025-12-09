DROP TABLE IF EXISTS cms_pages CASCADE;
DROP TABLE IF EXISTS cms_blocks CASCADE;

CREATE TABLE cms_blocks (
    id BIGSERIAL PRIMARY KEY,
    slug TEXT UNIQUE NOT NULL,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE iss_fetch_log (
    id BIGSERIAL PRIMARY KEY,
    fetched_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    source_url TEXT NOT NULL,
    payload JSONB NOT NULL
);

CREATE TABLE telemetry_legacy (
    id BIGSERIAL PRIMARY KEY,
    recorded_at TIMESTAMPTZ NOT NULL,
    voltage NUMERIC(6,2) NOT NULL,
    temp NUMERIC(6,2) NOT NULL,
    source_file TEXT NOT NULL
);

CREATE TABLE osdr_items (
    id BIGSERIAL PRIMARY KEY,
    dataset_id TEXT,
    title TEXT,
    status TEXT,
    updated_at TIMESTAMPTZ,
    inserted_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    raw JSONB NOT NULL
);

CREATE TABLE space_cache (
    id BIGSERIAL PRIMARY KEY,
    source TEXT NOT NULL,
    fetched_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    payload JSONB NOT NULL
);

CREATE INDEX ix_space_cache_source ON space_cache(source, fetched_at DESC);
CREATE INDEX ix_cms_blocks_slug_active ON cms_blocks(slug) WHERE is_active = TRUE;
CREATE UNIQUE INDEX ux_osdr_dataset_id ON osdr_items(dataset_id) WHERE dataset_id IS NOT NULL;

-- Seed with deliberately unsafe content for XSS practice
INSERT INTO cms_blocks (slug, title, content, is_active) VALUES
('welcome', 'Добро пожаловать', '<h3>Демо контент</h3><p> Этот текст хранится в БД</p>', true),
('unsafe', 'Небезопасный пример', '<script>console.log("XSS training")</script><p>Если вы видите всплывашку значит защита не работает</p>', true),
('dashboard_experiment', 'Эксперимент', '<div class="dashboard">Тестовый блок для админки</div>', true)
ON CONFLICT (slug) DO UPDATE SET 
    title = EXCLUDED.title,
    content = EXCLUDED.content,
    is_active = EXCLUDED.is_active,
    updated_at = NOW()