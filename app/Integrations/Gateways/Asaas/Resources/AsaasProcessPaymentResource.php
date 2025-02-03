<?php

namespace App\Integrations\Gateways\Asaas\Resources;

use App\Enums\SaleStatusEnum;
use App\Traits\Gateways\Asaas\AsaasFormatErrorsTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsaasProcessPaymentResource extends JsonResource
{
    use AsaasFormatErrorsTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'gateway_payment_status'      => $this['body']['status'] ?? null,
            'gateway_payment_id'          => $this['body']['id'] ?? null,
            'gateway_payment_approved_at' => !empty($this['body']['confirmedDate']) ? \Carbon\Carbon::parse($this['body']['confirmedDate'])->toDateTimeString() : null,
            'gateway_payment_detail'      => !empty($this['body']) ? $this->getGatewayPaymentBody($this['body']) : null,
            'request_status_code'         => $this['status_code'] ?? null,
            'sale_status_enum'            => $this->getStatusSale($this['body']['status'] ?? null),
            'errors'                      => $this->formatErrors($this['body']['errors'] ?? null, $this['status_code'] ?? null),
        ];
    }

    private function getGatewayPaymentBody($body)
    {
        $body['billetUrl'] = $body['bankSlipUrl'] ?? null;

        return $body;
    }

    private function getStatusSale($status): string
    {
        return match ($status) {
            'CONFIRMED' => SaleStatusEnum::APPROVED->value,
            'OVERDUE'   => SaleStatusEnum::EXPIRED->value,
            'CANCELLED' => SaleStatusEnum::REFUSED->value,
            default     => SaleStatusEnum::PENDING->value,
        };
    }
}
