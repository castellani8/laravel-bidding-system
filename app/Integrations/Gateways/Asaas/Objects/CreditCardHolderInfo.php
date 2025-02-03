<?php

namespace App\Integrations\Gateways\Asaas\Objects;

readonly class CreditCardHolderInfo
{
    public function __construct(
        public string $name,
        public string $email,
        public string $cpfCnpj,
        public string $phone,
        public ?string $postalCode = null,
        public ?string $addressNumber = null,
        public ?string $addressComplement = null,
        public ?string $mobilePhone = null,
    ) {}
}
