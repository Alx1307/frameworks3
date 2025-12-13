import os
from dataclasses import dataclass
from dotenv import load_dotenv

load_dotenv()

@dataclass
class DatabaseConfig:
    host: str = os.getenv('PGHOST', 'db')
    port: int = int(os.getenv('PGPORT', '5432'))
    database: str = os.getenv('PGDATABASE', 'monolith')
    user: str = os.getenv('PGUSER', 'monouser')
    password: str = os.getenv('PGPASSWORD', 'monopass')
    
    @property
    def connection_string(self):
        return f"host={self.host} port={self.port} dbname={self.database} user={self.user} password={self.password}"

@dataclass
class AppConfig:
    generation_interval: int = int(os.getenv('GEN_PERIOD_SEC', '300'))
    csv_output_dir: str = os.getenv('CSV_OUT_DIR', '/data/csv')
    excel_output_dir: str = os.getenv('EXCEL_OUT_DIR', '/data/excel')
    
    voltage_min: float = 3.2
    voltage_max: float = 12.6
    temp_min: float = -50.0
    temp_max: float = 80.0

db_config = DatabaseConfig()
app_config = AppConfig()