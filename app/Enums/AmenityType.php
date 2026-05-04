<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AmenityType: int implements HasLabel
{
    case TEXT = 1;
    case TEXTAREA = 2;
    case TOGGLE = 3;
    case CHECKBOX = 4;
    case RADIO = 5;
    case NUMBER = 6;
    case SELECT = 7;
    case MULTI_SELECT = 8;
    case DATE = 9;
    case DATE_TIME = 10;
    case URL = 11;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TEXT => 'Text',
            self::TEXTAREA => 'Textarea',
            self::TOGGLE => 'Toggle',
            self::CHECKBOX => 'Checkbox',
            self::RADIO => 'Radio',
            self::NUMBER => 'Number',
            self::SELECT => 'Select',
            self::MULTI_SELECT => 'Multi Select',
            self::DATE => 'Date',
            self::DATE_TIME => 'Date & Time',
            self::URL => 'URL',
        };
    }
}
