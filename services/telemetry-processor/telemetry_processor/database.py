import psycopg2
import psycopg2.extras
from typing import List
import logging

from .generator import TelemetryRecord
from .config import db_config

logger = logging.getLogger(__name__)

class DatabaseManager:
    def __init__(self):
        self.conn = None
        self.connect()
    
    def connect(self):
        try:
            self.conn = psycopg2.connect(
                host=db_config.host,
                port=db_config.port,
                database=db_config.database,
                user=db_config.user,
                password=db_config.password
            )
            logger.info("Database connection established")
        except Exception as e:
            logger.error(f"Database connection failed: {e}")
            raise
    
    def save_records(self, records: List[TelemetryRecord]):
        if not records:
            return
        
        try:
            cursor = self.conn.cursor()
            
            insert_query = """
            INSERT INTO telemetry_legacy 
            (recorded_at, voltage, temp, source_file)
            VALUES (%s, %s, %s, %s)
            """
            
            data_to_insert = [
                (r.timestamp, r.voltage, r.temperature, r.source_file)
                for r in records
            ]
            
            psycopg2.extras.execute_batch(cursor, insert_query, data_to_insert)
            
            self.conn.commit()
            cursor.close()
            
            logger.info(f"Successfully saved {len(records)} records to database")
            
        except Exception as e:
            logger.error(f"Error saving records to database: {e}")
            self.conn.rollback()
            raise
    
    def check_connection(self) -> bool:
        try:
            cursor = self.conn.cursor()
            cursor.execute("SELECT 1")
            cursor.close()
            return True
        except Exception as e:
            logger.error(f"Database connection check failed: {e}")
            return False
    
    def close(self):
        if self.conn:
            self.conn.close()
            logger.info("Database connection closed")