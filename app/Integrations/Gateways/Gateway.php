<?php

namespace App\Integrations\Gateways;

use App\Enums\HttpMethodEnum;
use App\Integrations\Gateways\Asaas\Asaas;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use RuntimeException;

abstract class Gateway implements GatewayInterface
{
    use Conditionable;

    protected PendingRequest $http;

    protected string $version;

    protected ?array $errors;

    protected ?string $baseUrl;

    protected ?array $headers = [];

    protected ?array $formatted = [];


    public static function instance(): Asaas
    {
        $className = "App\\Integrations\\Gateways\\Asaas\\Asaas";

        if (class_exists($className)) {
            return new $className();
        }

        throw new RuntimeException("Gateway {$className} not found.");
    }

    public function withHeaders(array $headers): static
    {
        $this->headers += $headers;

        return $this;
    }

    public function perform(HttpMethodEnum $method, $endpoint, ?array $params = []): array
    {
        $http = Http::withHeaders($this->headers)
            ->withoutVerifying()
            ->acceptJson()
            ->asJson()
            ->baseUrl("{$this->baseUrl}{$this->version}/")
            ->{$method->value}(
                $endpoint,
                $params
            );

        return [
            'status_code' => $http->getStatusCode(),
            'body'        => json_decode($http->body(), true),
        ];
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
