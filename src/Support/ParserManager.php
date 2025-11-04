<?php

namespace Placetopay\CamaraComercioBogotaSdk\Support;

use PlacetoPay\Base\Constants\Operations;
use Placetopay\CamaraComercioBogotaSdk\Exceptions\ParserException;
use Placetopay\CamaraComercioBogotaSdk\Parsers\FinancialParser;
use PlacetoPay\Tangram\Entities\BaseSettings;

class ParserManager
{
    protected const OPERATIONS_PARSERS = [
        Operations::SALE => FinancialParser::class,
    ];

    protected array $parsers = [];
    protected BaseSettings $settings;

    public function __construct(BaseSettings $settings)
    {
        $this->settings = $settings;
    }

    public function getParser(string $operation)
    {
        $this->validateOperation($operation);

        if (!isset($this->parsers[$operation])) {
            $this->createParser($operation);
        }

        return $this->parsers[$operation];
    }

    protected function createParser(string $operation): void
    {
        $parserName = self::OPERATIONS_PARSERS[$operation];
        $this->parsers[$operation] = new $parserName($this->settings);
    }

    /**
     * @throws ParserException
     */
    protected function validateOperation(string $operation): void
    {
        if (!isset(self::OPERATIONS_PARSERS[$operation])) {
            throw ParserException::invalidOperation($operation);
        }
    }
}
