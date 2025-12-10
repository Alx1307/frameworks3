class OsdrItem {
    constructor({ 
      id, 
      dataset_id, 
      title, 
      status, 
      updated_at, 
      inserted_at, 
      raw 
    }) {
      this.id = id;
      this.dataset_id = dataset_id;
      this.title = title;
      this.status = status;
      this.updated_at = updated_at;
      this.inserted_at = inserted_at;
      this.raw = raw;
    }
  
    toJSON() {
      return {
        id: this.id,
        dataset_id: this.dataset_id,
        title: this.title,
        status: this.status,
        updated_at: this.updated_at,
        inserted_at: this.inserted_at,
        raw: this.raw
      };
    }
  
    static fromDatabase(row) {
      return new OsdrItem({
        id: row.id,
        dataset_id: row.dataset_id,
        title: row.title,
        status: row.status,
        updated_at: row.updated_at,
        inserted_at: row.inserted_at,
        raw: row.raw
      });
    }
  
    static sPick(obj, keys) {
      for (const key of keys) {
        if (obj[key] && (typeof obj[key] === 'string' || typeof obj[key] === 'number')) {
          return String(obj[key]);
        }
      }
      return null;
    }
  
    static tPick(obj, keys) {
      for (const key of keys) {
        if (obj[key]) {
          if (typeof obj[key] === 'string') {
            const date = new Date(obj[key]);
            if (!isNaN(date.getTime())) {
              return date;
            }
          }
          if (typeof obj[key] === 'number') {
            return new Date(obj[key] * 1000);
          }
        }
      }
      return null;
    }
  }
  
  module.exports = OsdrItem;