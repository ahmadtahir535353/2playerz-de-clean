<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ThumbnailImage extends Component
{
    /**
     * Create a new component instance.
     */

    public $ThumbnailImage;
    public function __construct($ThumbnailImage = null)
    {
        $this->ThumbnailImage = $ThumbnailImage;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.thumbnail-image');
    }
}
