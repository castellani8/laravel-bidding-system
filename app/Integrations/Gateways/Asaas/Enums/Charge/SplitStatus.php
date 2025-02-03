<?php

namespace App\Integrations\Gateways\Asaas\Enums\Charge;

enum SplitStatus: string
{
    case PENDING         = 'PENDING';
    case AWAITING_CREDIT = 'AWAITING_CREDIT';
    case CANCELLED       = 'CANCELLED';
    case DONE            = 'DONE';
    case REFUSED         = 'REFUSED';
}
