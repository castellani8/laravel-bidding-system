<?php

namespace App\Integrations\Gateways\Asaas\Enums\Charge;

enum RefundStatus: string
{
    case PENDING   = 'PENDING';
    case CANCELLED = 'CANCELLED';
    case DONE      = 'DONE';
}
