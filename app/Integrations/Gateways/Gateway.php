<?php

namespace App\Integrations\Gateways;

use App\Enums\HttpMethod;
use App\Models\Gateway as GatewayModel;
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

    private static GatewayModel $gateway;

    public static function instance(GatewayModel $gateway)
    {
        self::$gateway = $gateway;
        $gatewayName   = Str::of($gateway->gateway_type_enum->name)->lower()->camel()->ucfirst();
        $className     = "App\\Integrations\\Gateways\\$gatewayName\\$gatewayName";

        if (class_exists($className)) {
            return new $className($gateway);
        }

        throw new RuntimeException("Gateway {$className} not found.");
    }

    public function withHeaders(array $headers): static
    {
        $this->headers += $headers;

        return $this;
    }

    public function perform(HttpMethod $method, $endpoint, ?array $params = []): array
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

    public function getModel(): GatewayModel
    {
        return self::$gateway;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
