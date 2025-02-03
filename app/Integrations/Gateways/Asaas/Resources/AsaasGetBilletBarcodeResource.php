<?php

namespace App\Integrations\Gateways\Asaas\Resources;

use App\Traits\Gateways\Asaas\AsaasFormatErrorsTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsaasGetBilletBarcodeResource extends JsonResource
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
            'request_status_code' => $this['status_code'] ?? null,
            'errors'              => $this->formatErrors($this['body']['errors'] ?? null, $this['status_code'] ?? null),
            'billet_number'       => $this['body']['identificationField'] ?? null,
            'billet_bar_code'     => $this['body']['barCode'] ?? null,
            'billet_url'          => $this['payment_detail']['billetUrl'] ?? null,
        ];
    }
}
