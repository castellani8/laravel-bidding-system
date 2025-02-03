<?php

namespace App\Integrations\Gateways\Asaas\Enums\Charge;

enum DiscountType: string
{
    case FIXED      = 'FIXED';
    case PERCENTAGE = 'PERCENTAGE';
}
