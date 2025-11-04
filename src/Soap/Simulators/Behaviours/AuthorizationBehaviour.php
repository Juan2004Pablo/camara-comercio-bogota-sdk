<?php

namespace DummyNamespace\Soap\Simulators\Behaviours;

use Placetopay\SoapClient\Response;

class AuthorizationBehaviour extends BaseSimulatorBehaviour
{
    protected const CASES = [
        '000000001' => 'failed',
        '000000002' => 'rejected',

    ];

    public function resolve(array $request): Response
    {
        $case = self::CASES[$request['account']] ?? 'success';

        return $this->$case();
    }

    public function success(): Response
    {
        return new Response([]);
    }

    public function failed(): Response
    {
        return new Response([]);
    }

    public function rejected(): Response
    {
        return new Response([]);
    }
}
