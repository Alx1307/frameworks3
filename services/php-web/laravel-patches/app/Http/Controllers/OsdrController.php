<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OsdrController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = $request->query('limit', 20);
            $limit = max(1, min($limit, 100));
            
            $oldBase = getenv('RUST_BASE') ?: 'http://rust_iss:3000';
            $json = @file_get_contents($oldBase.'/osdr/list?limit='.$limit);
            $data = $json ? json_decode($json, true) : ['items' => []];
            $items = $data['items'] ?? [];

            $flattenedItems = $this->flattenOsdrLegacy($items);
            
            $collection = collect($flattenedItems);
            
            // Фильтрация по поиску
            if ($request->has('search') && $request->search) {
                $search = strtolower($request->search);
                $collection = $collection->filter(function ($item) use ($search) {
                    return str_contains(strtolower($item['dataset_id'] ?? ''), $search) ||
                           str_contains(strtolower($item['title'] ?? ''), $search) ||
                           str_contains(strtolower($item['status'] ?? ''), $search);
                });
            }
            
            // Фильтрация по наличию URL
            if ($request->has('has_url') && $request->has_url) {
                $collection = $collection->filter(function ($item) {
                    return !empty($item['rest_url']);
                });
            }
            
            // Сортировка
            $sortColumn = $request->get('sort_column', 'title');
            $sortDirection = $request->get('sort_direction', 'asc');
            
            $collection = $collection->sortBy(function ($item) use ($sortColumn) {
                return $item[$sortColumn] ?? '';
            }, SORT_REGULAR, $sortDirection === 'desc');
            
            // Пагинация
            $perPage = $request->get('per_page', 50);
            $currentPage = $request->get('page', 1);
            
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $collection->forPage($currentPage, $perPage)->values(),
                $collection->count(),
                $perPage,
                $currentPage,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
            
            // Статистика
            $withUrlCount = collect($flattenedItems)->filter(function ($item) {
                return !empty($item['rest_url']);
            })->count();

            return view('osdr-new', [
                'items' => $paginator,
                'src'   => $oldBase.'/osdr/list?limit='.$limit,
                'withUrlCount' => $withUrlCount,
            ]);
            
        } catch (\Exception $e) {
            Log::error('OSDR legacy fetch failed: ' . $e->getMessage());
            
            return view('osdr-new', [
                'items' => collect([]),
                'src'   => 'fallback',
                'error' => 'Сервер данных временно недоступен',
                'withUrlCount' => 0,
            ]);
        }
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
                        'id'          => $row['id'] ?? null,
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
            if (is_array($v) && (isset($v['REST_URL']) || isset($v['rest_url']))) {
                return true;
            }
        }
        return false;
    }
}