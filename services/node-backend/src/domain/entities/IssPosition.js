class IssPosition {
  constructor({ id, fetched_at, source_url, payload }) {
    this.id = id;
    this.fetched_at = fetched_at;
    this.source_url = source_url;
    this.payload = payload;
  }

  toJSON() {
    return {
      id: this.id,
      fetched_at: this.fetched_at,
      source_url: this.source_url,
      payload: this.payload
    };
  }

  static fromDatabase(row) {
    return new IssPosition({
      id: row.id,
      fetched_at: row.fetched_at,
      source_url: row.source_url,
      payload: row.payload
    });
  }
}

module.exports = IssPosition;