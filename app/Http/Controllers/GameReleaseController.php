<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameReleasePageSetting;
use Illuminate\Http\Request;

class GameReleaseController extends Controller
{
    /**
     * Display all games release list
     */
    public function index()
    {
        $pageSetting = GameReleasePageSetting::where('page_type', GameReleasePageSetting::PAGE_TYPE_ALL)->first();
        
        $games = Game::orderedByReleaseDate()->get();
        
        // Group games by year and month for better display
        $gamesByPeriod = $this->groupGamesByPeriod($games);
        
        return view('game-releases.index', compact('pageSetting', 'gamesByPeriod', 'games'));
    }

    /**
     * Display PlayStation games
     */
    public function playstation()
    {
        $pageSetting = GameReleasePageSetting::where('page_type', GameReleasePageSetting::PAGE_TYPE_PLAYSTATION)->first();
        
        $games = Game::where('playstation', true)
            ->orderedByReleaseDate()
            ->get();
        
        $gamesByPeriod = $this->groupGamesByPeriod($games);
        
        return view('game-releases.platform', [
            'pageSetting' => $pageSetting,
            'gamesByPeriod' => $gamesByPeriod,
            'games' => $games,
            'platform' => 'playstation',
            'platformName' => 'PlayStation',
        ]);
    }

    /**
     * Display Xbox games
     */
    public function xbox()
    {
        $pageSetting = GameReleasePageSetting::where('page_type', GameReleasePageSetting::PAGE_TYPE_XBOX)->first();
        
        $games = Game::where('xbox', true)
            ->orderedByReleaseDate()
            ->get();
        
        $gamesByPeriod = $this->groupGamesByPeriod($games);
        
        return view('game-releases.platform', [
            'pageSetting' => $pageSetting,
            'gamesByPeriod' => $gamesByPeriod,
            'games' => $games,
            'platform' => 'xbox',
            'platformName' => 'Xbox',
        ]);
    }

    /**
     * Display Nintendo games
     */
    public function nintendo()
    {
        $pageSetting = GameReleasePageSetting::where('page_type', GameReleasePageSetting::PAGE_TYPE_NINTENDO)->first();
        
        $games = Game::where('nintendo', true)
            ->orderedByReleaseDate()
            ->get();
        
        $gamesByPeriod = $this->groupGamesByPeriod($games);
        
        return view('game-releases.platform', [
            'pageSetting' => $pageSetting,
            'gamesByPeriod' => $gamesByPeriod,
            'games' => $games,
            'platform' => 'nintendo',
            'platformName' => 'Nintendo',
        ]);
    }

    /**
     * Group games by year and month
     */
    private function groupGamesByPeriod($games)
    {
        $grouped = [];
        
        foreach ($games as $game) {
            if ($game->release_date) {
                $year = $game->release_date->format('Y');
                $month = $game->release_date->format('m');
                $monthName = $game->release_date->format('F Y');
                
                if (!isset($grouped[$year])) {
                    $grouped[$year] = [];
                }
                
                if (!isset($grouped[$year][$month])) {
                    $grouped[$year][$month] = [
                        'name' => $monthName,
                        'games' => [],
                    ];
                }
                
                $grouped[$year][$month]['games'][] = $game;
            } elseif ($game->release_month) {
                $parts = explode('-', $game->release_month);
                $year = $parts[0];
                $month = $parts[1];
                $monthName = date('F Y', strtotime($game->release_month . '-01'));
                
                if (!isset($grouped[$year])) {
                    $grouped[$year] = [];
                }
                
                if (!isset($grouped[$year][$month])) {
                    $grouped[$year][$month] = [
                        'name' => $monthName,
                        'games' => [],
                    ];
                }
                
                $grouped[$year][$month]['games'][] = $game;
            } elseif ($game->release_year) {
                $year = $game->release_year;
                
                if (!isset($grouped[$year])) {
                    $grouped[$year] = [];
                }
                
                if (!isset($grouped[$year]['year_only'])) {
                    $grouped[$year]['year_only'] = [
                        'name' => $year,
                        'games' => [],
                    ];
                }
                
                $grouped[$year]['year_only']['games'][] = $game;
            } else {
                // Games without release date
                if (!isset($grouped['TBA'])) {
                    $grouped['TBA'] = [];
                }
                
                if (!isset($grouped['TBA']['tba'])) {
                    $grouped['TBA']['tba'] = [
                        'name' => 'To Be Announced',
                        'games' => [],
                    ];
                }
                
                $grouped['TBA']['tba']['games'][] = $game;
            }
        }
        
        // Sort months within each year
        foreach ($grouped as $year => &$months) {
            if ($year !== 'TBA') {
                ksort($months);
            }
        }
        
        return $grouped;
    }
}
