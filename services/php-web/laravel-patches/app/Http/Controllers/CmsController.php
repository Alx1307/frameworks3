<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CmsController extends Controller 
{
    private $cacheTtl = 10;
    
    private function getApiBaseUrl(): string
    {
        $inDocker = file_exists('/.dockerenv') || getenv('IN_DOCKER');
        
        if ($inDocker) {
            return 'http://node_backend:3001';
        }
        
        return env('NODE_API_URL', 'http://localhost:8082');
    }
    
    public function page(string $slug) 
    {
        $apiUrl = $this->getApiBaseUrl();
        
        $response = Http::timeout(5)->get("{$apiUrl}/api/cms/block/{$slug}");
        
        if (!$response->successful()) {
            abort(404, "CMS страница '$slug' не найдена");
        }
        
        $data = $response->json();
        
        if (!$data['success'] || !isset($data['data'])) {
            abort(404, "CMS страница '$slug' не найдена");
        }
        
        $block = $data['data'];
        
        return response()->view('cms.page', [
            'title' => $block['title'], 
            'html' => new HtmlString($block['content']),
            'page' => (object) $block
        ]);
    }

    public function admin()
    {
        try {
            $apiUrl = $this->getApiBaseUrl();
            
            $blocksResponse = Http::timeout(5)->get("{$apiUrl}/api/cms/blocks");
            
            if ($blocksResponse->successful()) {
                $blocksData = $blocksResponse->json();
                $blocks = $blocksData['success'] ? collect($blocksData['data']) : collect();
            } else {
                $blocks = collect();
            }
            
            $dashboardResponse = Http::timeout(5)->get("{$apiUrl}/api/cms/block/dashboard_experiment");
            
            $dashboardBlock = null;
            if ($dashboardResponse->successful()) {
                $dashboardData = $dashboardResponse->json();
                if ($dashboardData['success'] && isset($dashboardData['data'])) {
                    $dashboardBlock = (object) $dashboardData['data'];
                }
            }
            
            return view('cms-admin', [
                'blocks' => $blocks,
                'dashboardBlock' => $dashboardBlock,
                'nodeApiUrl' => $apiUrl
            ]);
            
        } catch (\Exception $e) {
            Log::error('CMS Admin error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('cms-admin', [
                'blocks' => collect(),
                'dashboardBlock' => null,
                'error' => 'Ошибка загрузки данных. Проверьте подключение к Node.js API.'
            ]);
        }
    }
    
    public function create()
    {
        return view('cms.edit', [
            'block' => null,
            'nodeApiUrl' => $this->getApiBaseUrl(),
            'isCreate' => true
        ]);
    }
    
    public function store(Request $request)
    {
        try {
            $apiUrl = $this->getApiBaseUrl();
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'required|string|regex:/^[a-z0-9-_]+$/|unique:cms_blocks,slug',
                'content' => 'required|string',
                'is_active' => 'nullable|boolean'
            ]);
            
            $data = [
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'content' => $validated['content'],
                'is_active' => $request->boolean('is_active', true)
            ];
            
            $response = Http::timeout(10)
                ->post("{$apiUrl}/api/cms/block", $data);
            
            if (!$response->successful()) {
                $error = $response->json()['message'] ?? 'Ошибка создания блока';
                return back()->withInput()->with('error', $error);
            }
            
            $responseData = $response->json();
            
            if (!$responseData['success']) {
                return back()->withInput()->with('error', $responseData['message'] ?? 'Ошибка создания блока');
            }
            
            return redirect('/cms-admin')->with('success', 'Блок успешно создан!');
            
        } catch (\Exception $e) {
            Log::error('CMS Store error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ошибка создания: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $apiUrl = $this->getApiBaseUrl();
            
            $response = Http::timeout(5)->get("{$apiUrl}/api/cms/blocks");
            
            if (!$response->successful()) {
                return redirect('/cms-admin')->with('error', 'Ошибка загрузки данных');
            }
            
            $data = $response->json();
            
            if (!$data['success'] || !isset($data['data'])) {
                return redirect('/cms-admin')->with('error', 'Блоки не найдены');
            }
            
            $block = null;
            foreach ($data['data'] as $item) {
                if ($item['id'] == $id) {
                    $block = $item;
                    break;
                }
            }
            
            if (!$block) {
                return redirect('/cms-admin')->with('error', 'Блок не найден');
            }
            
            return view('cms.edit', [
                'block' => $block,
                'nodeApiUrl' => $apiUrl,
                'isCreate' => false
            ]);
            
        } catch (\Exception $e) {
            Log::error('CMS Edit error: ' . $e->getMessage());
            return redirect('/cms-admin')->with('error', 'Ошибка загрузки блока: ' . $e->getMessage());
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $apiUrl = $this->getApiBaseUrl();
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'required|string|regex:/^[a-z0-9-_]+$/',
                'content' => 'required|string',
                'is_active' => 'nullable|boolean'
            ]);
            
            $data = [
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'content' => $validated['content'],
                'is_active' => $request->boolean('is_active', true)
            ];
            
            $response = Http::timeout(10)
                ->put("{$apiUrl}/api/cms/block/{$id}", $data);
            
            if (!$response->successful()) {
                $error = $response->json()['message'] ?? 'Ошибка обновления блока';
                return back()->withInput()->with('error', $error);
            }
            
            $responseData = $response->json();
            
            if (!$responseData['success']) {
                return back()->withInput()->with('error', $responseData['message'] ?? 'Ошибка обновления блока');
            }
            
            return redirect('/cms-admin')->with('success', 'Блок успешно обновлен!');
            
        } catch (\Exception $e) {
            Log::error('CMS Update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ошибка обновления: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $apiUrl = $this->getApiBaseUrl();
            
            $response = Http::timeout(10)
                ->delete("{$apiUrl}/api/cms/block/{$id}");
            
            if (!$response->successful()) {
                $error = $response->json()['message'] ?? 'Ошибка удаления блока';
                return redirect('/cms-admin')->with('error', $error);
            }
            
            $responseData = $response->json();
            
            if (!$responseData['success']) {
                return redirect('/cms-admin')->with('error', $responseData['message'] ?? 'Ошибка удаления блока');
            }
            
            return redirect('/cms-admin')->with('success', 'Блок успешно удален!');
            
        } catch (\Exception $e) {
            Log::error('CMS Delete error: ' . $e->getMessage());
            return redirect('/cms-admin')->with('error', 'Ошибка удаления: ' . $e->getMessage());
        }
    }
}