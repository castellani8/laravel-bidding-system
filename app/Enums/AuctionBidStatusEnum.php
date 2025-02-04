<?php

namespace App\Enums;

use App\Traits\Enums\EnumTrait;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AuctionBidStatusEnum: string implements HasLabel, HasColor
{
    use EnumTrait;

    case APPROVED = 'APPROVED';

    case DECLINED = 'DECLINED';

    case PENDING  = 'PENDING';

    public function getLabel(): string
    {
        return match ($this) {
            self::APPROVED => 'Approved',
            self::DECLINED => 'Declined',
            self::PENDING  => 'Pending',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::APPROVED => 'success',
            self::DECLINED => 'danger',
            self::PENDING  => 'warning',
        };
    }
}
