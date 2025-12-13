import random
from datetime import datetime
from typing import Dict, List
from dataclasses import dataclass

from .config import app_config

@dataclass
class TelemetryRecord:
    timestamp: datetime
    voltage: float
    temperature: float
    is_operational: bool
    sensor_id: str
    source_file: str
    
    @classmethod
    def generate_random(cls) -> 'TelemetryRecord':
        timestamp = datetime.now()
        
        return cls(
            timestamp=timestamp,
            voltage=round(random.uniform(
                app_config.voltage_min, 
                app_config.voltage_max
            ), 2),
            temperature=round(random.uniform(
                app_config.temp_min, 
                app_config.temp_max
            ), 2),
            is_operational=random.choice([True, False]),
            sensor_id=f"SENSOR_{random.randint(1, 1000):04d}",
            source_file=f"telemetry_{timestamp.strftime('%Y%m%d_%H%M%S')}.csv"
        )
    
    def to_csv_dict(self) -> Dict[str, str]:
        return {
            'timestamp': self.timestamp.strftime('%Y-%m-%d %H:%M:%S'),
            'voltage': f"{self.voltage:.2f}",
            'temperature': f"{self.temperature:.2f}",
            'is_operational': 'TRUE' if self.is_operational else 'FALSE',
            'sensor_id': self.sensor_id,
            'source_file': self.source_file
        }
    
    def to_db_dict(self) -> Dict:
        return {
            'recorded_at': self.timestamp,
            'voltage': self.voltage,
            'temp': self.temperature,
            'source_file': self.source_file
        }


class TelemetryGenerator:
    def __init__(self, records_per_batch: int = 10):
        self.records_per_batch = records_per_batch
    
    def generate_batch(self) -> List[TelemetryRecord]:
        batch = []
        
        for _ in range(self.records_per_batch):
            record = TelemetryRecord.generate_random()
            batch.append(record)
        
        return batch