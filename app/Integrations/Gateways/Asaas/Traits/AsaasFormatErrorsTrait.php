<?php

namespace App\Integrations\Gateways\Asaas\Traits;

use Illuminate\Http\Response;

trait AsaasFormatErrorsTrait
{
    private function formatErrors($errors = null, $requestStatusCode = null)
    {
        $formattedErrors = [];

        if ($requestStatusCode === Response::HTTP_UNAUTHORIZED) {
            $formattedErrors[] = [
                'message'      => 'Não foi possível se  autenticar com o Asaas. Por favor verique se a chave de API está correta.',
                'gateway_code' => null,
            ];

            return $formattedErrors;
        }

        if (is_null($errors)) {
            return null;
        }

        if (is_string($errors)) {
            $formattedErrors[] = [
                'message'      => $errors,
                'gateway_code' => null,
            ];

            return $formattedErrors;
        }

        foreach ($errors as $error) {
            $formattedErrors[] = [
                'message'      => $error['description'] ?? null,
                'gateway_code' => $error['code'] ?? null,
            ];
        }

        return $formattedErrors;
    }
}
