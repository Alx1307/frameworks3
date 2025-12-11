<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class IssController extends Controller
{
    private $cacheTtl = 10;
    
    private function getApiBaseUrl(): string
    {
        $inDocker = file_exists('/.dockerenv') || getenv('IN_DOCKER');
        
        if ($inDocker) {
            return 'http://node_backend:3001';
        }
        
        return 'http://localhost:8082';
    }
    
    public function newIndex()
    {
        $cacheKey = 'iss_page_data_' . floor(time() / $this->cacheTtl);
        
        $data = Cache::remember($cacheKey, $this->cacheTtl, function () {
            $initialData = $this->fetchInitialData();
            $trendData = $this->fetchTrendData();
            
            return [
                'initialData' => $initialData,
                'trendData' => $trendData,
                'nodeBackendAvailable' => !empty($initialData['payload']) && !($initialData['is_fallback'] ?? false),
                'fetchedAt' => now()->format('Y-m-d H:i:s'),
                'cacheTime' => $this->cacheTtl,
            ];
        });
        
        $data['apiEndpoints'] = [
            'latest' => route('iss.api.latest'),
            'trend' => route('iss.api.trend'),
            'fetch' => route('iss.api.trigger-fetch'),
            'history' => route('iss.api.history'),
        ];
        
        return view('iss-new', $data);
    }

    public function apiLatest()
    {
        $cacheKey = 'iss_latest_api_' . floor(time() / 5);
        
        $data = Cache::remember($cacheKey, 5, function () {
            return $this->fetchFromNodeBackend('/api/iss/latest');
        });
        
        return response()->json($data);
    }
    
    public function apiTrend()
    {
        $cacheKey = 'iss_trend_api_' . floor(time() / 10);
        
        $data = Cache::remember($cacheKey, 10, function () {
            return $this->fetchFromNodeBackend('/api/iss/trend');
        });
        
        return response()->json($data);
    }
    
    public function apiTriggerFetch()
    {
        try {
            $apiBase = $this->getApiBaseUrl();
            $response = Http::timeout(5)
                ->post($apiBase . '/api/iss/fetch');
            
            if ($response->successful()) {
                Cache::forget('iss_latest_api_' . floor(time() / 5));
                Cache::forget('iss_trend_api_' . floor(time() / 10));
                
                return response()->json([
                    'success' => true,
                    'message' => 'Данные успешно обновлены',
                    'data' => $response->json()
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка обновления данных'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('ISS trigger fetch error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Сервер обработки данных недоступен'
            ], 503);
        }
    }
    
    private function fetchInitialData()
    {
        try {
            $apiBase = $this->getApiBaseUrl();
            $response = Http::timeout(2)
                ->get($apiBase . '/api/iss/latest');
            
            if ($response->successful()) {
                $data = $response->json();
                $data['is_fallback'] = false;
                return $data;
            }
            
            return $this->getFallbackData();
            
        } catch (\Exception $e) {
            Log::warning('ISS initial data fetch failed: ' . $e->getMessage());
            return $this->getFallbackData();
        }
    }
    
    private function fetchTrendData()
    {
        try {
            $apiBase = $this->getApiBaseUrl();
            $response = Http::timeout(3)
                ->get($apiBase . '/api/iss/trend');
            
            if ($response->successful()) {
                $data = $response->json();
                $data['is_fallback'] = false;
                return $data;
            }
            
            return $this->getFallbackTrendData();
            
        } catch (\Exception $e) {
            Log::warning('ISS trend data fetch failed: ' . $e->getMessage());
            return $this->getFallbackTrendData();
        }
    }

    private function fetchFromNodeBackend($endpoint)
    {
        try {
            $apiBase = $this->getApiBaseUrl();
            $response = Http::timeout(3)->get($apiBase . $endpoint);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception('API returned status: ' . $response->status());
            
        } catch (\Exception $e) {
            Log::error('Node.js backend request failed: ' . $e->getMessage());
            
            if ($endpoint === '/api/iss/latest') {
                return $this->getFallbackData();
            } else {
                return $this->getFallbackTrendData();
            }
        }
    }

    public function apiHistory(Request $request)
    {
        try {
            $limit = (int) $request->query('limit', 50);
            $limit = max(1, min($limit, 100));
            
            $cacheKey = 'iss_history_api_' . $limit . '_' . floor(time() / 30);
            
            $data = Cache::remember($cacheKey, 30, function () use ($limit) {
                try {
                    $apiBase = $this->getApiBaseUrl();
                    $response = Http::timeout(5)
                        ->get($apiBase . '/api/iss/history', ['limit' => $limit]);
                    
                    if ($response->successful()) {
                        $result = $response->json();
                        
                        if (!isset($result['points'])) {
                            throw new \Exception('Invalid response structure');
                        }
                        
                        return $result;
                    }
                    
                    throw new \Exception('API returned status: ' . $response->status());
                    
                } catch (\Exception $e) {
                    Log::warning('ISS history fetch failed: ' . $e->getMessage());
                    return $this->getDemoHistory($limit);
                }
            });
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            Log::error('API History error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
                'demo_data' => $this->getDemoHistory(50)
            ], 500);
        }
    }

    private function getDemoHistory($limit = 50)
    {
        $points = [];
        $now = time();
        $baseSpeed = 27500;
        $baseAltitude = 420;
        
        for ($i = 0; $i < $limit; $i++) {
            $timestamp = $now - ($i * 120);
            $points[] = [
                'lat' => 51.5074 + sin($i * 0.1) * 10,
                'lon' => -0.1278 + cos($i * 0.1) * 10,
                'altitude' => $baseAltitude + (rand(-100, 100) / 100),
                'velocity' => $baseSpeed + rand(-50, 50),
                'timestamp' => $timestamp,
                'fetched_at' => date('c', $timestamp)
            ];
        }
        
        return [
            'success' => true,
            'count' => count($points),
            'points' => $points,
            'is_demo' => true
        ];
    }
    
    private function getFallbackData()
    {
        return [
            'id' => null,
            'fetched_at' => now()->format('Y-m-d H:i:s'),
            'source_url' => 'fallback',
            'payload' => [
                'name' => 'iss',
                'id' => 25544,
                'latitude' => 51.5074,
                'longitude' => -0.1278,
                'altitude' => 420,
                'velocity' => 27600,
                'visibility' => 'daylight',
                'footprint' => 4534.17,
                'timestamp' => time(),
                'daynum' => 2460456.5,
                'solar_lat' => 0,
                'solar_lon' => 0,
                'units' => 'kilometers'
            ],
            'is_fallback' => true
        ];
    }

    private function getFallbackTrendData()
    {
        return [
            'movement' => false,
            'delta_km' => 0.0,
            'dt_sec' => 0.0,
            'velocity_kmh' => null,
            'from_time' => null,
            'to_time' => null,
            'from_lat' => null,
            'from_lon' => null,
            'to_lat' => null,
            'to_lon' => null,
            'points' => [],
            'is_fallback' => true
        ];
    }
}