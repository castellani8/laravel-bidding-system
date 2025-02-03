<?php

namespace App\Integrations\Gateways\Asaas\Resources;

use App\Traits\Gateways\Asaas\AsaasFormatErrorsTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsaasGetPixQrCodeResource extends JsonResource
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
            'pix_copy_past'       => $this['body']['payload'] ?? null,
            'pix_encoded_image'   => $this['body']['encodedImage'] ?? null,
            'pix_date_expiration' => $this['body']['expirationDate'] ?? null,
        ];
    }
}
