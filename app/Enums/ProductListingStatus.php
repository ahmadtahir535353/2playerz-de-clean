<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Facades\Lang;

enum ProductListingStatus: int implements HasLabel
{
    case PENDING = 0;
    case APPROVED = 1;
    case REJECTED = 2;
    case SOLD = 3;

    public function getLabel(): ?string
    {
        return Lang::get('messages.product_listings_status.' . $this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'primary',
            self::REJECTED => 'danger',
            self::SOLD => 'success',
        };
    }
}
