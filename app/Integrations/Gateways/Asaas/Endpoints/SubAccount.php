<?php

namespace App\Integrations\Gateways\Asaas\Endpoints;

use App\Enums\HttpMethodEnum;
use App\Integrations\Gateways\Asaas\Enums\SubAccount\CompanyType;

trait SubAccount
{
    public function createSubAccount(
        string $name,
        string $email,
        string $cpfCnpj,
        string $birthDate,
        string $address,
        string $addressNumber,
        string $province,
        string $postalCode,
        string $mobilePhone,
        ?CompanyType $companyType = null,
        ?string $site = null,
        ?string $complement = null,
    ): array {
        $params = compact(
            'name',
            'email',
            'cpfCnpj',
            'birthDate',
            'address',
            'addressNumber',
            'province',
            'postalCode',
            'companyType',
            'mobilePhone',
            'site',
            'complement'
        );

        return $this->perform(
            method: HttpMethodEnum::POST,
            endpoint: 'accounts',
            params: $params
        );
    }

    public function getAllSubAccounts(): array
    {
        return $this->perform(
            method: HttpMethodEnum::GET,
            endpoint: 'accounts'
        );
    }

    public function showSubAccount($id): array
    {
        $params = compact('id');

        return $this->perform(
            method: HttpMethodEnum::GET,
            endpoint: 'accounts',
            params: $params
        );
    }
}
