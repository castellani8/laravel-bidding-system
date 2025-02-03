<?php

namespace App\Filament\Helpers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class ColumnHelper
{
    public static function goTo(string $link, string $label, ?string $tooltip = ''): HtmlString
    {
        return new HtmlString(Blade::render('filament::components.link', [
            'color' => 'primary',
            'tooltip' => $tooltip,
            'href' => $link,
            'target' => '_blank',
            'slot' => $label,
            'icon' => 'heroicon-o-arrow-top-right-on-square',
        ]));
    }
}
