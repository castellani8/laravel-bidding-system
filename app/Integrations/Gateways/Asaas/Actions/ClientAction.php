<?php

namespace App\Integrations\Gateways\Asaas\Actions;

trait ClientAction
{
    public function createCustomer($data): array
    {
        return $this->createCustomerApi(
            name: $data['customer']['name'],
            cpfCnpj: $data['customer']['document'],
            email: $data['customer']['email'] ?? null,
            phone: $data['customer']['phone'] ?? null,
            postalCode: $data['customer']['postal_code'] ?? null,
            addressNumber: $data['customer']['address_number'] ?? null,
        );
    }
}
