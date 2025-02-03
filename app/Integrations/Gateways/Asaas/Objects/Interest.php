<?php

namespace App\Integrations\Gateways\Asaas\Objects;

readonly class Interest
{
    // Percentual de juros ao mês sobre o valor da cobrança para pagamento após o vencimento

    public function __construct(
        public int $value
    ) {}
}
