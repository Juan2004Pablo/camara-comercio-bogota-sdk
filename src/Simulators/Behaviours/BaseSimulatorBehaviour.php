<?php

namespace Placetopay\CamaraComercioBogotaSdk\Simulators\Behaviours;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

abstract class BaseSimulatorBehaviour
{
    protected const CASES = [];

    abstract public function resolve(RequestInterface $request): Response;

    public static function create(): self
    {
        return new static();
    }

    public function response($code, $body, $headers = [], $reason = null): Response
    {
        if (is_array($body)) {
            $body = json_encode($body, true);
        }

        return new Response($code, $headers, utf8_decode($body), '1.1', utf8_decode($reason));
    }
}
