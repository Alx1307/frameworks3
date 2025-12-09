<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CmsController extends Controller 
{
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

    public function admin()
    {
        try {
            $blocks = DB::table('cms_blocks')
                ->where('is_active', true)
                ->orderBy('updated_at', 'desc')
                ->get();
            
            $dashboardBlock = DB::selectOne(
                "SELECT content FROM cms_blocks WHERE slug='dashboard_experiment' AND is_active = TRUE LIMIT 1"
            );
            
            return view('cms-admin', [
                'blocks' => $blocks,
                'dashboardBlock' => $dashboardBlock
            ]);
            
        } catch (\Exception $e) {
            return view('cms-admin', [
                'blocks' => collect(),
                'dashboardBlock' => null,
                'error' => $e->getMessage()
            ]);
        }
    }
}