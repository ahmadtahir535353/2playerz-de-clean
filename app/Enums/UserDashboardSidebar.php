<?php

namespace App\Enums;

enum UserDashboardSidebar: int
{
    case PRODUCT_LISTINGS = 1;
    case FAVOURITE_LISTINGS = 2;
    case QUOTE_LISTINGS = 3;
    case PRODUCT_REVIEWS = 4;
    case PRODUCT_REPORTS = 5;
    case SETTINGS = 6;
}
