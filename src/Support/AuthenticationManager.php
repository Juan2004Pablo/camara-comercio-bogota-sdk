<?php

namespace Placetopay\CamaraComercioBogotaSdk\Support;

use Placetopay\CamaraComercioBogotaSdk\Constants\Endpoints;
use Placetopay\CamaraComercioBogotaSdk\Entities\Settings;
use Placetopay\CamaraComercioBogotaSdk\Exceptions\AuthenticationException;
use Placetopay\CamaraComercioBogotaSdk\Parsers\AuthenticationParser;
use PlacetoPay\Base\Constants\Operations;
use PlacetoPay\Base\Messages\AdministrativeTransaction;
use PlacetoPay\Tangram\Contracts\CarrierContract;
use PlacetoPay\Tangram\Entities\CarrierDataObject;
use Psr\SimpleCache\CacheInterface;

class AuthenticationManager
{
    public const AUTH_ENDPOINT = Endpoints::AUTHENTICATION;
    public const TOKEN_CACHE_PREFIX = 'camara-comercio-bogota-sdk-token';

    private Settings $settings;
    private CacheInterface $cache;
    private AuthenticationParser $parser;
    private CarrierContract $carrier;

    public function __construct(Settings $settings, CarrierContract $carrier)
    {
        $this->carrier = $carrier;
        $this->settings = $settings;
        $this->cache = $settings->cache();
        $this->parser = new AuthenticationParser();
    }

    public function token()
    {
        if (!$this->cache->has(self::TOKEN_CACHE_PREFIX)) {
            $this->authenticate();
        }

        return $this->cache->get(self::TOKEN_CACHE_PREFIX);
    }

    protected function authenticate()
    {
        $carrierDataObject = $this->parseRequest(new AdministrativeTransaction([]));

        $this->carrier->request($carrierDataObject);

        $transaction = $this->parseResponse($carrierDataObject);

        $this->cache->set(
            self::TOKEN_CACHE_PREFIX,
            $transaction->additional('token'),
            $transaction->additional('expiredAt')
        );
    }

    protected function parseRequest(AdministrativeTransaction $transaction): CarrierDataObject
    {
        $carrierDataObject = new CarrierDataObject(Operations::AUTHENTICATION, $transaction, [
            'method' => 'POST',
            'endpoint' => $this->settings->url() . self::AUTH_ENDPOINT,
        ]);

        $carrierDataObject->setRequest($this->parser->parserRequest($carrierDataObject));

        return $carrierDataObject;
    }

    /**
     * @throws AuthenticationException
     */
    private function parseResponse(CarrierDataObject $carrierDataObject): AdministrativeTransaction
    {
        return $carrierDataObject->error()
            ? $this->parser->errorHandler($carrierDataObject)
            : $this->parser->parserResponse($carrierDataObject);
    }
}
