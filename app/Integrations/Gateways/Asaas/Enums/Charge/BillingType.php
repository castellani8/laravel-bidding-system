<?php

namespace App\Integrations\Gateways\Asaas\Enums\Charge;

enum BillingType: string
{
    case BOLETO      = 'BOLETO';
    case CREDIT_CARD = 'CREDIT_CARD';
    case UNDEFINED   = 'UNDEFINED';
    case DEBIT_CARD  = 'DEBIT_CARD';
    case TRANSFER    = 'TRANSFER';
    case DEPOSIT     = 'DEPOSIT';
    case PIX         = 'PIX';
}
