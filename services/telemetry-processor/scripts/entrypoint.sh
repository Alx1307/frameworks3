#!/bin/bash
set -e

echo "Starting Telemetry Processor..."
echo "Configuration:"
echo "  PG_HOST: ${PGHOST:-db}"
echo "  PG_DB: ${PGDATABASE:-monolith}"
echo "  Interval: ${GEN_PERIOD_SEC:-300}s"
echo "  CSV output: ${CSV_OUT_DIR:-/data/csv}"
echo "  Excel output: ${EXCEL_OUT_DIR:-/data/excel}"

if [ "${WAIT_FOR_DB:-true}" = "true" ]; then
    echo "Waiting for database to be ready..."
    until pg_isready -h "${PGHOST:-db}" -p "${PGPORT:-5432}" -U "${PGUSER:-monouser}"; do
        sleep 2
    done
    echo "Database is ready!"
fi

mkdir -p "${CSV_OUT_DIR:-/data/csv}"
mkdir -p "${EXCEL_OUT_DIR:-/data/excel}"

exec python -m telemetry_processor.main