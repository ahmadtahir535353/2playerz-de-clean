<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReleaseCalendarBadgeColor extends Model
{
    protected $table = 'release_calendar_badge_colors';

    protected $fillable = [
        'playstation_bg',
        'playstation_text',
        'xbox_bg',
        'xbox_text',
        'nintendo_bg',
        'nintendo_text',
        'ps_plus_bg',
        'ps_plus_text',
        'game_pass_bg',
        'game_pass_text',
    ];

    /**
     * Get the single settings record (first row).
     */
    public static function settings(): ?self
    {
        return static::first();
    }

    /**
     * Get badge colors as array for views (with defaults).
     */
    public static function colorsForView(): array
    {
        $s = static::settings();
        $def = [
            'playstation' => ['bg' => '#4a4a4a', 'text' => '#e0e0e0'],
            'xbox' => ['bg' => '#4a4a4a', 'text' => '#e0e0e0'],
            'nintendo' => ['bg' => '#4a4a4a', 'text' => '#e0e0e0'],
            'ps_plus' => ['bg' => '#1976d2', 'text' => '#ffffff'],
            'game_pass' => ['bg' => '#107c10', 'text' => '#ffffff'],
        ];
        if (!$s) {
            return $def;
        }
        return [
            'playstation' => ['bg' => $s->playstation_bg ?: $def['playstation']['bg'], 'text' => $s->playstation_text ?: $def['playstation']['text']],
            'xbox' => ['bg' => $s->xbox_bg ?: $def['xbox']['bg'], 'text' => $s->xbox_text ?: $def['xbox']['text']],
            'nintendo' => ['bg' => $s->nintendo_bg ?: $def['nintendo']['bg'], 'text' => $s->nintendo_text ?: $def['nintendo']['text']],
            'ps_plus' => ['bg' => $s->ps_plus_bg ?: $def['ps_plus']['bg'], 'text' => $s->ps_plus_text ?: $def['ps_plus']['text']],
            'game_pass' => ['bg' => $s->game_pass_bg ?: $def['game_pass']['bg'], 'text' => $s->game_pass_text ?: $def['game_pass']['text']],
        ];
    }
}
