<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class VideoEmbed extends Component
{
    /**
     * Create a new component instance.
     */

    public $videoEmbedCode;

    public function __construct($videoEmbedCode = null)
    {
        $this->videoEmbedCode = $videoEmbedCode;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.video-embed');
    }
}
