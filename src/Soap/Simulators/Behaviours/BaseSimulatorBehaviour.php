<?php

namespace DummyNamespace\Soap\Simulators\Behaviours;

use Placetopay\SoapClient\Response;

abstract class BaseSimulatorBehaviour
{
    protected const CASES = [];

    abstract public function resolve(array $request): Response;

    public static function create(): self
    {
        return new static();
    }
}
