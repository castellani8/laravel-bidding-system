<?php

namespace App\Enums;

use App\Traits\Enums\EnumTrait;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AuctionStatusEnum: string implements HasLabel, HasColor
{
    use EnumTrait;

    case ACTIVE   = 'ACTIVE';

    case INACTIVE = 'INACTIVE';
    
    case FINISHED = 'FINISHED';

    public function getLabel(): string
    {
        return match ($this) {
            self::INACTIVE  => 'Inactive',
            self::ACTIVE    => 'Active',
            self::FINISHED  => 'Finished',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::INACTIVE  => 'warning',
            self::ACTIVE    => 'success',
            self::FINISHED  => 'danger',
        };
    }
}
