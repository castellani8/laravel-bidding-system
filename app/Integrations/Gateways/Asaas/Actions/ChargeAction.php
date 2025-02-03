<?php

namespace App\Integrations\Gateways\Asaas\Actions;

use App\Integrations\Gateways\Asaas\Enums\Charge\BillingType;
use App\Integrations\Gateways\Asaas\Objects\CreditCard;
use App\Integrations\Gateways\Asaas\Objects\CreditCardHolderInfo;

trait ChargeAction
{
    public function processCreditCardPayment($data, $customer): array
    {
        return $this->processCreditCardPaymentApi(
            customer: $customer['customer']['external_id'],
            billingType: BillingType::CREDIT_CARD,
            dueDate: $data['payment']['dueDate'],
            remoteIp: $data['payment']['remoteIp'],
            value: $data['payment']['value'],
            externalReference: $data['payment']['externalReference'],
            creditCard: (new CreditCard(
                holderName: $data['payment']['holderName'],
                number: $data['payment']['creditCardNumber'],
                expiryMonth: $data['payment']['expiryMonth'],
                expiryYear: $data['payment']['expiryYear'],
                cvv: $data['payment']['cvv']
            )),
            creditCardHolderInfo: (new CreditCardHolderInfo(
                name: $data['customer']['name'],
                email: $data['customer']['email'],
                cpfCnpj: $data['customer']['document'],
                phone: $data['customer']['phone'],
                postalCode: $data['customer']['postal_code'] ?? null,
                addressNumber: $data['customer']['address_number'] ?? null,
            )),
            installmentCount: $data['payment']['installmentCount'],
            installmentValue: $data['payment']['installmentValue'],
            description: $data['payment']['description'],
        );
    }

    public function processPixPayment($data, $customer = null): array
    {
        return $this->processPixPaymentApi(
            customer: $data['customer']['external_id'] ?? $customer['customer']['external_id'],
            billingType: BillingType::PIX,
            value: $data['payment']['value'],
            description: $data['payment']['description'],
            dueDate: $data['payment']['dueDate'],
            externalReference: $data['payment']['externalReference'],
        );
    }

    public function processBilletPayment($data, $customer): array
    {
        return $this->processBilletPaymentApi(
            customer: $customer['customer']['external_id'],
            billingType: BillingType::BOLETO,
            value: $data['payment']['value'],
            description: $data['payment']['description'],
            dueDate: $data['payment']['dueDate'],
            externalReference: $data['payment']['externalReference'],
        );
    }

    public function getPayment($externalId): array
    {
        return $this->getPaymentApi($externalId);
    }
}
