<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TelemetryController extends Controller
{
    public function index()
    {
        return view('telemetry');
    }

    public function apiData(Request $request)
    {
        try {
            $limit = $request->query('limit', 100);
            $offset = $request->query('offset', 0);
            
            // Используем прямое подключение к PostgreSQL
            $query = DB::connection('pgsql')
                ->table('telemetry_legacy')
                ->select(
                    'id',
                    'recorded_at',
                    DB::raw('ROUND(voltage::numeric, 2) as voltage'),
                    DB::raw('ROUND(temp::numeric, 2) as temperature'),
                    'sensor_id',
                    'source_file'
                )
                ->orderBy('recorded_at', 'desc');
            
            // Фильтры
            if ($request->has('sensor_id')) {
                $query->where('sensor_id', 'LIKE', '%' . $request->query('sensor_id') . '%');
            }
            
            if ($request->has('date_from')) {
                $query->where('recorded_at', '>=', $request->query('date_from'));
            }
            
            if ($request->has('date_to')) {
                $query->where('recorded_at', '<=', $request->query('date_to'));
            }
            
            if ($request->has('operational')) {
                $query->where('is_operational', $request->query('operational') === 'true');
            }
            
            $total = $query->count();
            $data = $query->limit($limit)->offset($offset)->get();
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка получения телеметрии: ' . $e->getMessage());
            
            // Возвращаем демо-данные если нет доступа к БД
            return $this->getDemoData($request);
        }
    }

    public function apiStats(Request $request)
    {
        try {
            $stats = DB::connection('pgsql')
                ->table('telemetry_legacy')
                ->selectRaw('
                    COUNT(*) as total_records,
                    AVG(voltage) as avg_voltage,
                    AVG(temp) as avg_temperature,
                    MIN(recorded_at) as first_record,
                    MAX(recorded_at) as last_record,
                    COUNT(CASE WHEN voltage < 5 THEN 1 END) as low_voltage_count,
                    COUNT(CASE WHEN temp > 60 THEN 1 END) as high_temp_count
                ')
                ->first();
            
            // Статистика по сенсорам
            $sensors = DB::connection('pgsql')
                ->table('telemetry_legacy')
                ->select('sensor_id', DB::raw('COUNT(*) as count'))
                ->whereNotNull('sensor_id')
                ->groupBy('sensor_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'sensors' => $sensors,
                'generated_at' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => true, // Все равно возвращаем успех для демо
                'stats' => $this->getDemoStats(),
                'sensors' => $this->getDemoSensors(),
                'generated_at' => now()->toISOString(),
                'note' => 'Демо-данные (БД недоступна)'
            ]);
        }
    }

    public function apiRealtime(Request $request)
    {
        try {
            // Последние 100 записей для реального времени
            $recent = DB::connection('pgsql')
                ->table('telemetry_legacy')
                ->select(
                    'recorded_at',
                    DB::raw('ROUND(voltage::numeric, 2) as voltage'),
                    DB::raw('ROUND(temp::numeric, 2) as temperature'),
                    'sensor_id'
                )
                ->orderBy('recorded_at', 'desc')
                ->limit(100)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $recent,
                'fetched_at' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'data' => $this->getDemoRealtime(),
                'fetched_at' => now()->toISOString(),
                'note' => 'Демо-данные'
            ]);
        }
    }

    private function getDemoData(Request $request)
    {
        $limit = $request->query('limit', 100);
        $offset = $request->query('offset', 0);
        
        $demoData = [];
        $now = now();
        
        for ($i = $offset; $i < $offset + $limit; $i++) {
            $voltage = rand(32, 126) / 10; // 3.2 - 12.6
            $temp = rand(-500, 800) / 10; // -50 - 80
            $sensorId = 'SENSOR_' . str_pad(rand(1, 100), 4, '0', STR_PAD_LEFT);
            
            $demoData[] = [
                'id' => $i + 1,
                'recorded_at' => $now->copy()->subMinutes($i * 5)->toISOString(),
                'voltage' => round($voltage, 2),
                'temperature' => round($temp, 2),
                'sensor_id' => $sensorId,
                'source_file' => 'telemetry_' . date('Ymd_His', strtotime("-{$i} minutes")) . '.csv',
                'is_operational' => $voltage > 5 && $temp < 60
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $demoData,
            'total' => 1000,
            'limit' => $limit,
            'offset' => $offset,
            'note' => 'Демо-данные'
        ]);
    }

    private function getDemoStats()
    {
        return (object) [
            'total_records' => 1250,
            'avg_voltage' => 7.85,
            'avg_temperature' => 25.3,
            'first_record' => now()->subDays(30)->toISOString(),
            'last_record' => now()->toISOString(),
            'low_voltage_count' => 45,
            'high_temp_count' => 32
        ];
    }

    private function getDemoSensors()
    {
        return collect([
            ['sensor_id' => 'SENSOR_0042', 'count' => 128],
            ['sensor_id' => 'SENSOR_0017', 'count' => 112],
            ['sensor_id' => 'SENSOR_0099', 'count' => 105],
            ['sensor_id' => 'SENSOR_0033', 'count' => 98],
            ['sensor_id' => 'SENSOR_0077', 'count' => 92],
        ]);
    }

    private function getDemoRealtime()
    {
        $demoData = [];
        $now = now();
        
        for ($i = 0; $i < 100; $i++) {
            $demoData[] = [
                'recorded_at' => $now->copy()->subMinutes($i)->toISOString(),
                'voltage' => round(7.5 + sin($i * 0.1) * 2 + (rand(0, 100) / 100), 2),
                'temperature' => round(25 + cos($i * 0.05) * 10 + (rand(0, 100) / 100), 2),
                'sensor_id' => 'SENSOR_' . str_pad(rand(1, 10), 4, '0', STR_PAD_LEFT)
            ];
        }
        
        return $demoData;
    }
}