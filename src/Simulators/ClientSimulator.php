<?php

namespace Placetopay\CamaraComercioBogotaSdk\Simulators;

use GuzzleHttp\Psr7\Response;
use Placetopay\CamaraComercioBogotaSdk\Constants\Endpoints;
use Placetopay\CamaraComercioBogotaSdk\Simulators\Behaviours\BaseSimulatorBehaviour;
use Placetopay\CamaraComercioBogotaSdk\Simulators\Behaviours\ConsultInformationBehaviour;
use PlacetoPay\Tangram\Mock\Client\HttpClientMock;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ClientSimulator extends HttpClientMock
{
    protected const HANDLERS = [
        Endpoints::CONSULT_INFORMATION => ConsultInformationBehaviour::class,
    ];

    protected string $uri;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->uri = $config['base_uri'] ?? '';
    }

    protected function createResponse(RequestInterface $request, array $options): ResponseInterface
    {
        /** @var BaseSimulatorBehaviour $behaviour */
        $behaviour = self::HANDLERS[$request->getUri()->getPath()] ?? null;

        return !$behaviour ? new Response(404) : $behaviour::create()->resolve($request);
    }
}
