<?php

namespace App\Integrations\Gateways\Asaas\Objects;

readonly class CreditCard
{
    // Nome impresso no cartão
    // Número do cartão
    // Mês de expiração (ex: 06)
    // Ano de expiração com 4 dígitos (ex: 2019)
    // Código de segurança

    public function __construct(
        public string $holderName,
        public string $number,
        public string $expiryMonth,
        public string $expiryYear,
        public string $cvv,
    ) {}
}
