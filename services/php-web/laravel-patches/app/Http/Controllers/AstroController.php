<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AstroController extends Controller
{
    private function getApiBaseUrl(): string
    {
        $inDocker = file_exists('/.dockerenv') || getenv('IN_DOCKER');
        
        if ($inDocker) {
            return 'http://node_backend:3001/api';
        }
        
        return 'http://localhost:8082/api';
    }
    
    public function index()
    {
        return view('astronomy');
    }
    
    public function events(Request $request)
    {
        try {
            $apiBase = $this->getApiBaseUrl();
            $params = $request->query();
            
            $testResponse = Http::timeout(3)->get($apiBase . '/health');
            
            if ($testResponse->successful()) {
                $response = Http::timeout(10)
                    ->get($apiBase . '/astronomy/events', $params);
                
                if ($response->successful()) {
                    return response()->json($response->json());
                }
            }
            
        } catch (\Exception $e) {
            \Log::warning('Node.js API недоступен', [
                'error' => $e->getMessage(),
                'url' => $apiBase ?? 'unknown'
            ]);
        }
        
        return response()->json($this->getFallbackEvents($request));
    }
    
    private function getFallbackEvents(Request $request): array
    {
        $lat = $request->query('lat', 55.7558);
        $lon = $request->query('lon', 37.6176);
        $days = $request->query('days', 7);
        
        $now = new \DateTime();
        
        return [
            'success' => true,
            'data' => [
                'events' => [
                    [
                        'name' => 'ISS Flyby (Demo)',
                        'type' => 'satellite_flyby',
                        'time' => $now->modify('+1 hour')->format('c'),
                        'altitude' => '420 km',
                        'details' => 'International Space Station visible - DEMO DATA'
                    ],
                    [
                        'name' => 'Solar Eclipse (Demo)',
                        'type' => 'eclipse',
                        'time' => $now->modify('+24 hours')->format('c'),
                        'magnitude' => 0.85,
                        'details' => 'Partial solar eclipse - DEMO DATA'
                    ],
                    [
                        'name' => 'Meteor Shower (Demo)',
                        'type' => 'meteor_shower',
                        'time' => $now->modify('+48 hours')->format('c'),
                        'rate' => '60 per hour',
                        'details' => 'Annual meteor shower peak - DEMO DATA'
                    ]
                ]
            ],
            'metadata' => [
                'lat' => $lat,
                'lon' => $lon,
                'days' => $days,
                'note' => 'Demo data - Node.js API unavailable'
            ]
        ];
    }
}