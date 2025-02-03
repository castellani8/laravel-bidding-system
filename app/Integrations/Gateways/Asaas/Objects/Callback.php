<?php

namespace App\Integrations\Gateways\Asaas\Objects;

readonly class Callback
{
    // URL que o cliente será redirecionado após o pagamento com sucesso da fatura ou link de pagamento
    // Definir se o cliente será redirecionado automaticamente ou será apenas informado com um botão para retornar ao site. O padrão é true, caso queira desativar informar false

    public function __construct(
        public string $successUrl,
        public ?bool $autoRedirect = false
    ) {}
}
