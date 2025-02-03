<?php

namespace App\Integrations\Gateways\Asaas\Endpoints;

use App\Enums\HttpMethod;
use App\Integrations\Gateways\Asaas\Enums\Charge\BillingType;
use App\Integrations\Gateways\Asaas\Objects\Callback;
use App\Integrations\Gateways\Asaas\Objects\CreditCard;
use App\Integrations\Gateways\Asaas\Objects\CreditCardHolderInfo;
use App\Integrations\Gateways\Asaas\Objects\Discount;
use App\Integrations\Gateways\Asaas\Objects\Fine;
use App\Integrations\Gateways\Asaas\Objects\Interest;
use App\Integrations\Gateways\Asaas\Resources\AsaasGetBilletBarcodeResource;
use App\Integrations\Gateways\Asaas\Resources\AsaasGetPaymentResource;
use App\Integrations\Gateways\Asaas\Resources\AsaasGetPixQrCodeResource;
use App\Integrations\Gateways\Asaas\Resources\AsaasProcessPaymentResource;

trait Charge
{
    public function allCharges(
        $customer = null,
        $customerGroupName = null,
        $billingType = null,
        $status = null,
        $subscription = null,
        $installment = null,
        $externalReference = null,
        $estimatedCreditDate = null,
        $pixQrCodeId = null,
        $anticipated = null,
        $user = null, // Email que criou a cobrança
        $limit = null,
        $dateCreated = [],
        $paymentDate = [],
        $dueDate = [],
    ): array {
        $params = compact(
            'customer',
            'customerGroupName',
            'billingType',
            'status',
            'subscription',
            'installment',
            'externalReference',
            'paymentDate',
            'estimatedCreditDate',
            'pixQrCodeId',
            'anticipated',
            'user',
            'limit',
            'dateCreated',
            'paymentDate',
            'dueDate'
        );

        return $this->perform(
            method: HttpMethod::GET,
            endpoint: 'payments',
            params: $params
        );
    }

    public function getPaymentApi($externalId): array
    {
        $response = $this->perform(
            HttpMethod::GET,
            "payments/{$externalId}",
        );

        return AsaasGetPaymentResource::make($response)->resolve();
    }

    public function getPixQrCode($externalId): array
    {
        $response = $this->perform(
            HttpMethod::GET,
            "payments/{$externalId}/pixQrCode",
        );

        return AsaasGetPixQrCodeResource::make($response)->resolve();
    }

    public function getBilletBarcode($externalId, $paymentDetail): array
    {
        $response = $this->perform(
            HttpMethod::GET,
            "payments/{$externalId}/identificationField",
        );

        $response['payment_detail'] = $paymentDetail;

        return AsaasGetBilletBarcodeResource::make($response)->resolve();
    }

    public function processBilletPaymentApi(
        $customer,
        $billingType,
        $value,
        $description,
        $dueDate,
        $externalReference,
    ): array {
        $params = compact(
            'customer',
            'billingType',
            'value',
            'description',
            'dueDate',
            'externalReference',
        );

        $response = $this->perform(
            method: HttpMethod::POST,
            endpoint: 'payments',
            params: $params
        );

        return AsaasProcessPaymentResource::make($response)->resolve();
    }

    public function processCreditCardPaymentApi(
        string $customer,
        BillingType $billingType,
        string $dueDate,
        string $remoteIp,
        float $value,
        string $externalReference,
        CreditCard $creditCard,  // If the charge is from credit card
        CreditCardHolderInfo $creditCardHolderInfo,  // Required if charge is credit card
        int $installmentCount,
        float $installmentValue,

        ?string $description = null,
        ?float $totalValue = null,
        ?Discount $discount = null,
        ?Interest $interest = null,
        ?Fine $fine = null,
        ?bool $postalService = false,
        ?array $split = [], // Array of split objects
        ?string $creditCardToken = null,  // Token is get in the first use of the card
        bool $authorizeOnly = false,
    ): array {
        $params = compact(
            'customer',
            'billingType',
            'dueDate',
            'remoteIp',
            'value',
            'description',
            'externalReference',
            'installmentCount',
            'totalValue',
            'installmentValue',
            'discount',
            'interest',
            'fine',
            'postalService',
            'split',
            'creditCard',
            'creditCardHolderInfo',
            'creditCardToken',
            'authorizeOnly'
        );

        $response = $this->perform(
            method: HttpMethod::POST,
            endpoint: 'payments',
            params: $params
        );

        return AsaasProcessPaymentResource::make($response)->resolve();
    }

    public function updateAsaasCharge(
        $externalId,
        ?string $customer = null,
        ?BillingType $billingType = null,
        ?string $dueDate = null,
        ?float $value = null,
        ?string $description = null,
        ?int $installmentCount = null,
        ?float $installmentValue = null,
        ?Discount $discount = null,
        ?Interest $interest = null,
        ?Fine $fine = null,
        ?bool $postalService = false,
        ?array $split = [], // Array of split objects
        ?Callback $callback = null
    ): array {
        $params = compact(
            'customer',
            'billingType',
            'dueDate',
            'value',
            'description',
            'installmentCount',
            'installmentValue',
            'discount',
            'interest',
            'fine',
            'postalService',
            'split',
            'callback'
        );

        $response = $this->perform(
            HttpMethod::POST,
            "payments/{$externalId}",
            $params
        );

        return $response;
    }

    public function tokenizeCreditCard(
        string $customer,
        CreditCard $creditCard,
        ?CreditCardHolderInfo $creditCardHolderInfo = null
    ): array {
        $params = compact(
            'customer',
            'creditCard',
            'creditCardHolderInfo'
        );

        return $this->perform(
            method: HttpMethod::POST,
            endpoint: 'creditCard/tokenize',
            params: $params
        );
    }

    public function payChargeWithCreditCard(
        $id,
        CreditCard $creditCard,
        CreditCardHolderInfo $creditCardHolderInfo,
        ?string $creditCardToken = null
    ): array {
        $params = compact(
            'creditCard',
            'creditCardHolderInfo',
            'creditCardToken'
        );

        $response = $this->perform(
            method: HttpMethod::POST,
            endpoint: "payments/{$id}/payWithCreditCard",
            params: $params
        );

        return $response;
    }

    public function showCharge($id): array
    {
        $response = $this->perform(
            method: HttpMethod::GET,
            endpoint: "payments/{$id}",
        );

        return $response;
    }

    public function destroyCharge($id): array
    {
        return $this->perform(
            method: HttpMethod::DELETE,
            endpoint: "payments/{$id}",
        );
    }

    public function restoreCharge($id): array
    {
        return $this->perform(
            method: HttpMethod::POST,
            endpoint: "payments/{$id}/restore",
        );
    }

    public function reversalCharge($id, float $value, string $description): array
    {
        $params = compact(
            'value',
            'description'
        );

        return $this->perform(
            method: HttpMethod::POST,
            endpoint: "payments/{$id}/refund",
            params: $params
        );
    }

    public function getBillOfExchangeNumber($id): array
    {
        return $this->perform(
            method: HttpMethod::GET,
            endpoint: "payments/{$id}/identificationField",
        );
    }

    private function processPixPaymentApi(
        $customer,
        $billingType,
        $value,
        $description,
        $dueDate,
        $externalReference,
    ): array {
        $params = compact(
            'customer',
            'billingType',
            'value',
            'description',
            'dueDate',
            'externalReference',
        );

        $response = $this->perform(
            method: HttpMethod::POST,
            endpoint: 'payments',
            params: $params
        );

        return AsaasProcessPaymentResource::make($response)->resolve();
    }
}
