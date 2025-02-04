<?php

namespace App\Integrations\Gateways\Asaas\Endpoints;

use App\Enums\HttpMethodEnum;
use App\Integrations\Gateways\Asaas\Resources\AsaasCreateCustomerInGatewayResource;

trait Client
{
    public function getAllCustomers(): array
    {
        return $this->perform(
            method: HttpMethodEnum::GET,
            endpoint: 'customers',
        );
    }

    public function createCustomerApi(
        string $name,
        string $cpfCnpj,
        ?string $email = null,
        ?string $phone = null,
        ?string $mobilePhone = null,
        ?string $groupName = null,
        ?string $postalCode = null,
        ?string $address = null,
        ?string $addressNumber = null,
        ?string $complement = null,
        ?string $province = null,
        ?string $externalReference = null,
        ?string $additionalEmails = null,
        ?string $municipalInscription = null,
        ?string $stateInscription = null,
        ?string $observations = null,
        ?string $company = null,
        ?bool $notificationDisabled = null,
    ): ?array {
        if (!$this->shouldCreateCustomerInGateway()) {
            return null;
        }

        $params = compact(
            'name',
            'email',
            'phone',
            'mobilePhone',
            'cpfCnpj',
            'postalCode',
            'address',
            'addressNumber',
            'complement',
            'province',
            'externalReference',
            'notificationDisabled',
            'additionalEmails',
            'municipalInscription',
            'stateInscription',
            'observations',
            'groupName',
            'company',
        );

        $response = $this->perform(
            method: HttpMethodEnum::POST,
            endpoint: 'customers',
            params: $params
        );

        $response = AsaasCreateCustomerInGatewayResource::make(
            ($response)
        )->resolve();

        $data['customer']['external_id'] = $response['gateway_customer_id'];

        return $data;
    }

    public function showCustomer($clientId): array
    {
        return $this->perform(
            method: HttpMethodEnum::GET,
            endpoint: "customers/{$clientId}"
        );
    }

    public function updateCustomer($clientId,
        ?string $name = null,
        ?string $cpfCnpj = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $mobilePhone = null,
        ?string $groupName = null,
        ?string $postalCode = null,
        ?string $address = null,
        ?string $addressNumber = null,
        ?string $complement = null,
        ?string $province = null,
        ?string $externalReference = null,
        ?string $additionalEmails = null,
        ?string $municipalInscription = null,
        ?string $stateInscription = null,
        ?string $observations = null,
        ?string $company = null,
        ?bool $notificationDisabled = null,
    ): array {
        $params = compact(
            'name',
            'email',
            'phone',
            'mobilePhone',
            'cpfCnpj',
            'postalCode',
            'address',
            'addressNumber',
            'complement',
            'province',
            'externalReference',
            'notificationDisabled',
            'additionalEmails',
            'municipalInscription',
            'stateInscription',
            'observations',
            'groupName',
            'company',
        );

        return $this->perform(
            method: HttpMethodEnum::POST,
            endpoint: "customers/{$clientId}",
            params: $params
        );
    }

    public function destroyCustomer($clientId): array
    {
        return $this->perform(
            method: HttpMethodEnum::DELETE,
            endpoint: "customers/{$clientId}"
        );
    }

    public function restoreCustomer($clientId): array
    {
        return $this->perform(
            method: HttpMethodEnum::POST,
            endpoint: "customers/{$clientId}/restore"
        );
    }
}
