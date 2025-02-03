<?php

namespace App\Integrations\Gateways\Asaas;

use App\Integrations\Gateways\Asaas\Actions\ChargeAction;
use App\Integrations\Gateways\Asaas\Actions\ClientAction;
use App\Integrations\Gateways\Asaas\Endpoints\Charge;
use App\Integrations\Gateways\Asaas\Endpoints\Client;
use App\Integrations\Gateways\Asaas\Endpoints\SubAccount;
use App\Integrations\Gateways\Gateway;
use App\Models\Gateway as GatewayModel;
use Illuminate\Support\Facades\Config;

class Asaas extends Gateway
{
    use Charge;
    use ChargeAction;
    use Client;
    use ClientAction;
    use SubAccount;

    protected string $version = 'v3';

    public function __construct(?GatewayModel $gateway = null)
    {
        $accessToken = is_null($gateway)
            ? Config::get('checkout.gateways.asaas.token')
            : $gateway->configuration['api_key'];

        $this->headers = [
            'access_token' => $accessToken,
        ];

        $this->baseUrl = Config::get('checkout.gateways.asaas.baseUrl');
    }

    public function shouldCreateCustomerInGateway(): bool
    {
        return true;
    }
}
