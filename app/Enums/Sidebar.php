<?php

namespace App\Enums;

enum Sidebar: int
{
    case STAFFS = 1;
    case POSTS = 2;
    case BULK_POST = 3;
    case EMOJIS = 4;
    case ALBUM_CATEGORIES = 5;
    case GALLERY_IMAGES = 6;
    case ALBUMS = 7;
    case CASH_PAYMENTS = 8;
    case SUBSCRIBED_USER_PLANS = 9;
    case PAGES = 10;
    case MENUS = 11;
    case RSS_FEED = 12;
    case NAVIGATION = 13;
    case POLLS = 14;
    case PLANS = 15;
    case CATEGORIES = 16;
    case SUB_CATEGORIES = 17;
    case ROLE_PERMISSSION = 18;
    case SEO_TOOLS = 19;
    case LANGUAGE = 20;
    case NEWS_LATTERS = 21;
    case COMMENTS = 22;
    case MAILS = 23;
    case AD_SPACE = 24;
    case CONTACTS = 25;
    case SETTINGS = 26;

    case GENERAL = 1;
    case CONTENT_INTERACTION = 2;
    case SOCIAL_MEDIA = 3;
    case COOKIE_WARNING = 4;
    case CMS = 5;
    case AD_MANAGEMENT = 6;
    case GENERAL_SETTINGS = 7;
    case ADVANCED_SETTINGS = 8;
    case THEME_CONFIGURATION = 9;
}
