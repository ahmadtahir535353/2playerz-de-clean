<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Concerns\HasName;

class SeoBox extends Field
{
    protected string $view = 'filament.forms.components.seo-box';

    protected array $fieldNames = [
        'title' => 'title',
        'content' => 'description',
        'seo_title' => 'title',
        'seo_description' => 'description',
        'focus_keyword' => 'focus_keyword',
    ];

    public function fieldNames(array $names): static
    {
        $this->fieldNames = array_merge($this->fieldNames, $names);

        return $this;
    }

    public function getFieldNames(): array
    {
        return $this->fieldNames;
    }
}

