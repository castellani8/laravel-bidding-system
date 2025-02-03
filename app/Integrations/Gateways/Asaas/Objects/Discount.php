<?php

namespace App\Integrations\Gateways\Asaas\Objects;

readonly class Discount
{
    // Valor percentual ou fixo de desconto a ser aplicado sobre o valor da cobrança
    // Dias antes do vencimento para aplicar desconto. Ex: 0 = até o vencimento, 1 = até um dia antes, 2 = até dois dias antes, e assim por diante
    // Se o valor do desconto é FIXED ou PERCENTAGE

    public function __construct(
        public int $value,
        public int $dueDateLimitDays,
        public string $type
    ) {}
}
