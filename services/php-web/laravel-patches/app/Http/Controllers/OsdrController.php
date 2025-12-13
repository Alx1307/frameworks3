<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class OsdrController extends Controller
{
    private $cacheTtl = 60;
    
    private function getApiBaseUrl(): string
    {
        $inDocker = file_exists('/.dockerenv') || getenv('IN_DOCKER');
        
        if ($inDocker) {
            return 'http://node_backend:3001';
        }
        
        return 'http://localhost:3001';
    }

    public function index(Request $request)
    {
        try {
            $limit = $request->query('limit', 20);
            $limit = max(1, min($limit, 100));
            
            $oldBase = getenv('RUST_BASE') ?: 'http://rust_iss:3000';
            $json = @file_get_contents($oldBase.'/osdr/list?limit='.$limit);
            $data = $json ? json_decode($json, true) : ['items' => []];
            $items = $data['items'] ?? [];

            return view('osdr', [
                'items' => $this->flattenOsdrLegacy($items),
                'src'   => $oldBase.'/osdr/list?limit='.$limit,
                'api_source' => 'rust_backend'
            ]);
            
        } catch (\Exception $e) {
            Log::error('OSDR legacy fetch failed: ' . $e->getMessage());
            
            return view('osdr', [
                'items' => [],
                'src'   => 'fallback',
                'error' => 'Сервер данных временно недоступен'
            ]);
        }
    }

    public function newIndex(Request $request)
    {
        $limit = $request->query('limit', 20);
        $limit = max(1, min($limit, 100));
        $page = $request->query('page', 1);
        
        $cacheKey = 'osdr_new_data_' . $limit . '_' . $page . '_' . floor(time() / $this->cacheTtl);
        
        $data = Cache::remember($cacheKey, $this->cacheTtl, function () use ($limit, $page) {
            $apiData = $this->fetchFromNodeBackend('/api/osdr/list?limit=' . $limit . '&page=' . $page);
            
            $items = $apiData['items'] ?? $apiData['datasets'] ?? [];
            
            return [
                'items' => $this->flattenOsdrNode($items),
                'summary' => $apiData['summary'] ?? null,
                'pagination' => $apiData['pagination'] ?? null,
                'nodeBackendAvailable' => true,
                'fetchedAt' => now()->format('Y-m-d H:i:s'),
                'cacheTime' => $this->cacheTtl,
            ];
        });
        
        $data['apiEndpoints'] = [
            'list' => route('osdr.api.list'),
            'summary' => route('osdr.api.summary'),
            'refresh' => route('osdr.api.refresh'),
            'base_url' => $this->getApiBaseUrl() . '/api/osdr'
        ];
        
        $data['currentParams'] = [
            'limit' => $limit,
            'page' => $page
        ];
        
        return view('osdr-new', $data);
    }
    
    public function apiList(Request $request)
    {
        try {
            $limit = (int) $request->query('limit', 20);
            $page = (int) $request->query('page', 1);
            $limit = max(1, min($limit, 100));
            
            $cacheKey = 'osdr_api_list_' . $limit . '_' . $page . '_' . floor(time() / 30);
            
            $data = Cache::remember($cacheKey, 30, function () use ($limit, $page) {
                $apiBase = $this->getApiBaseUrl();
                $response = Http::timeout(10)
                    ->get($apiBase . '/api/osdr/list', [
                        'limit' => $limit,
                        'page' => $page
                    ]);
                
                if ($response->successful()) {
                    $result = $response->json();
                    
                    if (isset($result['items']) || isset($result['datasets'])) {
                        $items = $result['items'] ?? $result['datasets'] ?? [];
                        $result['items'] = $this->flattenOsdrNode($items);
                    }
                    
                    return $result;
                }
                
                throw new \Exception('API returned status: ' . $response->status());
                
            });
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            Log::error('OSDR API list error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Сервер данных временно недоступен',
                'error' => $e->getMessage(),
                'demo_data' => $this->getDemoOsdrData()
            ], 503);
        }
    }
    
    private function flattenOsdrNode(array $items): array
    {
        $out = [];
        
        foreach ($items as $row) {
            $id = $row['id'] ?? null;
            $insertedAt = $row['inserted_at'] ?? $row['updated_at'] ?? null;
            $raw = $row['raw'] ?? $row;
            
            if (is_array($raw) && $this->looksOsdrDict($raw)) {
                foreach ($raw as $datasetId => $datasetData) {
                    if (!is_string($datasetId) || !str_starts_with($datasetId, 'OSD-')) {
                        continue;
                    }
                    
                    if (!is_array($datasetData)) {
                        $datasetData = ['REST_URL' => $datasetData];
                    }
                    
                    $restUrl = $datasetData['REST_URL'] ?? $datasetData['rest_url'] ?? $datasetData['rest'] ?? null;
                    $title = $datasetData['title'] ?? $datasetData['name'] ?? $datasetId;
                    
                    $out[] = [
                        'id'          => $id,
                        'dataset_id'  => $datasetId,
                        'title'       => $title,
                        'status'      => $row['status'] ?? null,
                        'updated_at'  => $row['updated_at'] ?? null,
                        'inserted_at' => $insertedAt,
                        'rest_url'    => $restUrl,
                        'raw'         => $datasetData,
                    ];
                }
            } 
            elseif (isset($row['dataset_id'])) {
                $out[] = $row;
            }
            else {
                $restUrl = is_array($raw) ? ($raw['REST_URL'] ?? $raw['rest_url'] ?? null) : null;
                $title = $row['title'] ?? (is_array($raw) ? ($raw['title'] ?? $raw['name'] ?? null) : null);
                
                $out[] = [
                    'id'          => $id,
                    'dataset_id'  => $row['dataset_id'] ?? null,
                    'title'       => $title,
                    'status'      => $row['status'] ?? null,
                    'updated_at'  => $row['updated_at'] ?? null,
                    'inserted_at' => $insertedAt,
                    'rest_url'    => $restUrl,
                    'raw'         => $raw,
                ];
            }
        }
        
        return $out;
    }
    
    private function flattenOsdrLegacy(array $items): array
    {
        $out = [];
        foreach ($items as $row) {
            $raw = $row['raw'] ?? [];
            if (is_array($raw) && $this->looksOsdrDict($raw)) {
                foreach ($raw as $k => $v) {
                    if (!is_array($v)) continue;
                    $rest = $v['REST_URL'] ?? $v['rest_url'] ?? $v['rest'] ?? null;
                    $title = $v['title'] ?? $v['name'] ?? null;
                    if (!$title && is_string($rest)) {
                        $title = basename(rtrim($rest, '/'));
                    }
                    $out[] = [
                        'id'          => $row['id'],
                        'dataset_id'  => $k,
                        'title'       => $title,
                        'status'      => $row['status'] ?? null,
                        'updated_at'  => $row['updated_at'] ?? null,
                        'inserted_at' => $row['inserted_at'] ?? null,
                        'rest_url'    => $rest,
                        'raw'         => $v,
                    ];
                }
            } else {
                $row['rest_url'] = is_array($raw) ? ($raw['REST_URL'] ?? $raw['rest_url'] ?? null) : null;
                $out[] = $row;
            }
        }
        return $out;
    }

    private function looksOsdrDict(array $raw): bool
    {
        foreach ($raw as $k => $v) {
            if (is_string($k) && str_starts_with($k, 'OSD-')) {
                return true;
            }
        }
        return false;
    }
    
    public function apiDataset(Request $request, $datasetId)
    {
        try {
            $cacheKey = 'osdr_dataset_' . $datasetId . '_' . floor(time() / 300);
            
            $data = Cache::remember($cacheKey, 300, function () use ($datasetId) {
                $apiBase = $this->getApiBaseUrl();
                $response = Http::timeout(15)
                    ->get($apiBase . '/api/osdr/dataset/' . $datasetId);
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                throw new \Exception('API returned status: ' . $response->status());
                
            });
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            Log::error('OSDR dataset fetch error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Датасет не найден или сервер недоступен',
                'dataset_id' => $datasetId
            ], 404);
        }
    }

    public function apiSummary()
    {
        try {
            $cacheKey = 'osdr_summary_' . floor(time() / 180);
            
            $data = Cache::remember($cacheKey, 180, function () {
                $apiBase = $this->getApiBaseUrl();
                $response = Http::timeout(5)
                    ->get($apiBase . '/api/space/summary');
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                throw new \Exception('API returned status: ' . $response->status());
                
            });
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            Log::error('OSDR summary fetch error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Сводная информация временно недоступна',
                'fallback_data' => $this->getFallbackSummary()
            ], 503);
        }
    }
    
    public function apiRefresh()
    {
        try {
            $apiBase = $this->getApiBaseUrl();
            $response = Http::timeout(5)
                ->post($apiBase . '/api/osdr/refresh');
            
            if ($response->successful()) {
                Cache::flush();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Кеш успешно обновлен',
                    'data' => $response->json()
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка обновления кеша'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('OSDR refresh error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Сервер обработки данных недоступен'
            ], 503);
        }
    }

    private function fetchFromNodeBackend($endpoint)
    {
        try {
            $apiBase = $this->getApiBaseUrl();
            $response = Http::timeout(10)->get($apiBase . $endpoint);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception('API returned status: ' . $response->status());
            
        } catch (\Exception $e) {
            Log::error('Node.js OSDR backend request failed: ' . $e->getMessage());
            
            if (str_contains($endpoint, 'list')) {
                return $this->getDemoOsdrData();
            } else {
                return $this->getFallbackSummary();
            }
        }
    }
    
    private function getDemoOsdrData()
    {
        $items = [];
        for ($i = 1; $i <= 10; $i++) {
            $items[] = [
                'id' => $i,
                'dataset_id' => 'OSD-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'title' => 'NASA OSDR Dataset #' . $i . ' - Biological Experiment',
                'status' => 'available',
                'updated_at' => now()->subDays(rand(1, 30))->format('Y-m-d H:i:s'),
                'inserted_at' => now()->subDays(rand(31, 90))->format('Y-m-d H:i:s'),
                'rest_url' => 'https://visualization.osdr.nasa.gov/biodata/api/v2/datasets/OSD-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'raw' => [
                    'dataset_id' => 'OSD-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'title' => 'NASA OSDR Dataset #' . $i,
                    'description' => 'Sample biological dataset from NASA OSDR',
                    'mission' => 'ISS',
                    'experiment_type' => 'Biology',
                    'organism' => 'Human',
                    'data_type' => 'Omics',
                    'size_gb' => rand(1, 100),
                    'records' => rand(1000, 100000)
                ]
            ];
        }
        
        return [
            'success' => true,
            'count' => count($items),
            'items' => $items,
            'summary' => [
                'total_datasets' => 1560,
                'last_updated' => now()->format('Y-m-d H:i:s'),
                'by_mission' => ['ISS' => 450, 'Ground' => 1110],
                'by_type' => ['Biology' => 890, 'Physics' => 420, 'Astronomy' => 250]
            ],
            'pagination' => [
                'current_page' => 1,
                'total_pages' => 156,
                'per_page' => 10,
                'total_items' => 1560
            ],
            'is_demo' => true,
            'fetched_at' => now()->format('Y-m-d H:i:s')
        ];
    }
    
    private function getFallbackSummary()
    {
        return [
            'success' => true,
            'summary' => [
                'total_datasets' => 1560,
                'last_updated' => now()->format('Y-m-d H:i:s'),
                'by_mission' => ['ISS' => 450, 'Ground' => 1110],
                'by_type' => ['Biology' => 890, 'Physics' => 420, 'Astronomy' => 250],
                'status' => 'active'
            ],
            'is_fallback' => true
        ];
    }
}