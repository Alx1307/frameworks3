<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class CmsController extends Controller 
{
    public function page(string $slug) 
    {
        $row = DB::selectOne(
            "SELECT id, slug, title, content, is_active, created_at, updated_at 
             FROM cms_blocks 
             WHERE slug = ? AND is_active = TRUE", 
            [$slug]
        );
        
        if (!$row) {
            abort(404, "CMS страница '$slug' не найдена");
        }
        
        $safeHtml = $this->sanitizeHtml($row->content);
        
        return response()->view('cms.page', [
            'title' => $row->title, 
            'html' => new HtmlString($safeHtml),
            'page' => $row
        ]);
    }

    public function admin()
    {
        try {
            $blocks = DB::table('cms_blocks')
                ->where('is_active', true)
                ->orderBy('updated_at', 'desc')
                ->select('id', 'slug', 'title', 'content', 'is_active', 'created_at', 'updated_at')
                ->get();
            
            $dashboardBlock = DB::table('cms_blocks')
                ->where('slug', 'dashboard_experiment')
                ->where('is_active', true)
                ->select('id', 'slug', 'title', 'content', 'created_at')
                ->first();
            
            return view('cms-admin', [
                'blocks' => $blocks,
                'dashboardBlock' => $dashboardBlock
            ]);
            
        } catch (\Exception $e) {
            \Log::error('CMS Admin error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('cms-admin', [
                'blocks' => collect(),
                'dashboardBlock' => null,
                'error' => 'Ошибка загрузки данных. Проверьте подключение к БД.'
            ]);
        }
    }
    
    private function sanitizeHtml(string $html): string
    {
        $dangerousTags = ['script', 'iframe', 'object', 'embed', 'link', 'meta', 'form'];
        $dangerousAttributes = ['onclick', 'onload', 'onerror', 'onmouseover', 'href="javascript:'];
        
        foreach ($dangerousTags as $tag) {
            $html = preg_replace("/<{$tag}[^>]*>.*?<\/{$tag}>/is", '', $html);
            $html = preg_replace("/<{$tag}[^>]*>/is", '', $html);
        }
        
        foreach ($dangerousAttributes as $attr) {
            $html = preg_replace("/\s+{$attr}=['\"][^'\"]*['\"]/i", '', $html);
        }
        
        return $html;
    }
}