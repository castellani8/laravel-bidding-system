<?php

namespace App\Integrations\Gateways\Asaas\Objects;

readonly class Fine
{
    // Percentual de multa sobre o valor da cobrança para pagamento após o vencimento
    // Se o valor do desconto é FIXED ou PERCENTAGE

    public function __construct(
        public int $value,
        public string $type
    ) {}
}
