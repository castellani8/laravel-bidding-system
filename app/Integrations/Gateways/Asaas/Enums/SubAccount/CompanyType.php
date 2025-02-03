<?php

namespace App\Integrations\Gateways\Asaas\Enums\SubAccount;

enum CompanyType: string
{
    case MEI         = 'MEI';
    case LIMITED     = 'LIMITED';
    case INDIVIDUAL  = 'INDIVIDUAL';
    case ASSOCIATION = 'ASSOCIATION';
}
