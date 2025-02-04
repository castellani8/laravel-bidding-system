<?php

namespace App\Enums;

use App\Traits\Enums\EnumTrait;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SaleStatusEnum: int implements HasColor, HasLabel
{
    use EnumTrait;

    case PENDING              = 1;
    case EXPIRED              = 2;
    case REFUSED              = 3;
    case APPROVED             = 4;
    case REFUNDED             = 5;
    case CHARGEBACK_REQUESTED = 6;
    case CHARGEBACK_DISPUTE   = 7;
    case CHARGEBACK_REVERSAL  = 8;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING              => 'Pendente',
            self::EXPIRED              => 'Expirada',
            self::REFUSED              => 'Recusada',
            self::APPROVED             => 'Aprovada',
            self::REFUNDED             => 'Estornado',
            self::CHARGEBACK_REQUESTED => 'Chargeback solicitado',
            self::CHARGEBACK_DISPUTE   => 'Chargeback em disputa',
            self::CHARGEBACK_REVERSAL  => 'Disputa vencida',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING              => 'warning',
            self::EXPIRED              => 'danger',
            self::REFUSED              => 'danger',
            self::APPROVED             => 'success',
            self::REFUNDED             => 'warning',
            self::CHARGEBACK_REQUESTED => 'danger',
            self::CHARGEBACK_DISPUTE   => 'warning',
            self::CHARGEBACK_REVERSAL  => 'danger',
        };
    }
}
