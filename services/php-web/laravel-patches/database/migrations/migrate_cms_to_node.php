<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

function migrateCmsToNode()
{
    $blocks = DB::table('cms_blocks')->get();
    
    $inDocker = file_exists('/.dockerenv') || getenv('IN_DOCKER');
    $nodeApiUrl = $inDocker ? 'http://node_backend:3001' : env('NODE_API_URL', 'http://localhost:8082');
    
    $migrated = 0;
    $failed = 0;
    
    foreach ($blocks as $block) {
        try {
            $response = Http::timeout(10)->post("{$nodeApiUrl}/api/cms/block", [
                'slug' => $block->slug,
                'title' => $block->title,
                'content' => $block->content,
                'is_active' => (bool) $block->is_active,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                if ($data['success']) {
                    echo "Мигрирован блок: {$block->slug}\n";
                    $migrated++;
                } else {
                    echo "Ошибка API: {$block->slug} - " . ($data['error']['message'] ?? 'Unknown error') . "\n";
                    $failed++;
                }
            } else {
                echo "HTTP ошибка: {$block->slug} - Status: " . $response->status() . "\n";
                $failed++;
            }
        } catch (\Exception $e) {
            echo "Ошибка соединения: {$block->slug} - " . $e->getMessage() . "\n";
            $failed++;
        }
    }
    
    echo "\nИтог: Успешно: {$migrated}, Ошибок: {$failed}\n";
}