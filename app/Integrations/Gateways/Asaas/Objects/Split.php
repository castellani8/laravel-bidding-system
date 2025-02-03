<?php

namespace App\Integrations\Gateways\Asaas\Objects;

readonly class Split
{
    // Identificador da carteira (retornado no momento da criação da conta)
    // Valor fixo a ser transferido para a conta quando a cobrança for recebida
    // Percentual sobre o valor líquido da cobrança a ser transferido quando for recebida
    // (Somente parcelamentos). Valor que será feito split referente ao valor total que será parcelado.\

    public function __construct(
        public string $walletId,
        public float $fixedValue,
        public float $percentualValue,
        public float $totalFixedValue,
    ) {}
}
