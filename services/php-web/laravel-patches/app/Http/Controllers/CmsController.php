<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CmsController extends Controller 
{
    /**
     * Отображение отдельных CMS страниц по slug
     */
    public function page(string $slug) 
    {
        $row = DB::selectOne(
            "SELECT title, content FROM cms_blocks WHERE slug = ? AND is_active = TRUE", 
            [$slug]
        );
        
        if (!$row) {
            abort(404, "CMS страница '$slug' не найдена");
        }
        
        return response()->view('cms.page', [
            'title' => $row->title, 
            'html' => $row->content
        ]);
    }
    
    /**
     * Админ-панель для управления CMS блоками
     */
    public function admin()
    {
        try {
            // Получаем все активные CMS блоки
            $blocks = DB::table('cms_blocks')
                ->where('is_active', true)
                ->orderBy('updated_at', 'desc')
                ->get();
            
            // Получаем специальный блок для dashboard (для отображения в админке)
            $dashboardBlock = DB::selectOne(
                "SELECT content FROM cms_blocks WHERE slug='dashboard_experiment' AND is_active = TRUE LIMIT 1"
            );
            
            return view('cms-admin', [
                'blocks' => $blocks,
                'dashboardBlock' => $dashboardBlock
            ]);
            
        } catch (\Exception $e) {
            // В случае ошибки БД возвращаем пустой список
            return view('cms-admin', [
                'blocks' => collect(),
                'dashboardBlock' => null,
                'error' => $e->getMessage()
            ]);
        }
    }
}