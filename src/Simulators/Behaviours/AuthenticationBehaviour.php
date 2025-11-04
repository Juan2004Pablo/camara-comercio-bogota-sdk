<?php

namespace Placetopay\CamaraComercioBogotaSdk\Simulators\Behaviours;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class AuthenticationBehaviour extends BaseSimulatorBehaviour
{
    protected const CASES = [
        'Basic MTExMTExMTE6MTExMTExMTE=' => 'rejected',
        'Basic MDAwMDA6MDAwMDA=' => 'failed',
    ];

    public function resolve(RequestInterface $request): Response
    {
        $case = self::CASES[$request->getHeaderLine('authorization')] ?? 'success';

        return $this->$case();
    }

    public function success(): Response
    {
        return $this->response(200, [
            'token_type' => 'Bearer',
            'access_token' => substr(rand(), 0, 20),
            'expires_in' => 3600,
        ]);
    }

    public function rejected(): Response
    {
        return $this->response(401, [
            'message' => 'unauthorized',
        ]);
    }

    public function failed(): Response
    {
        return $this->response(500, []);
    }
}
