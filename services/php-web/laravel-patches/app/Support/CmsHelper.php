<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CmsHelper
{
    private static function getApiBaseUrl(): string
    {
        $inDocker = file_exists('/.dockerenv') || getenv('IN_DOCKER');
        
        if ($inDocker) {
            return 'http://node_backend:3001';
        }
        
        return env('NODE_API_URL', 'http://localhost:8082');
    }
    
    public static function getBlock(string $slug, bool $useCache = true): string
    {
        $cacheKey = "cms_block_{$slug}";
        
        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $apiUrl = self::getApiBaseUrl();
        
        try {
            $response = Http::timeout(3)->get("{$apiUrl}/api/cms/block/{$slug}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success'] && isset($data['data']['content'])) {
                    $content = $data['data']['content'];
                    
                    if ($useCache) {
                        Cache::put($cacheKey, $content, now()->addMinutes(5));
                    }
                    
                    return $content;
                }
            }
        } catch (\Exception $e) {
            Log::warning('CMS block fetch error: ' . $e->getMessage(), [
                'slug' => $slug,
                'api_url' => $apiUrl
            ]);
        }
        
        return '<div class="alert alert-warning">CMS блок "' . htmlspecialchars($slug) . '" не найден</div>';
    }
    
    public static function clearCache(string $slug): void
    {
        $cacheKey = "cms_block_{$slug}";
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
    }
    
    public static function bladeDirective(): void
    {
        \Blade::directive('cms', function ($expression) {
            $slug = trim($expression, "'\"");
            return "<?php echo \App\Support\CmsHelper::getBlock('{$slug}'); ?>";
        });
    }
}