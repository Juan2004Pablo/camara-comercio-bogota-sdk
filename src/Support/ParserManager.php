<?php

namespace Placetopay\CamaraComercioBogotaSdk\Support;

use Placetopay\CamaraComercioBogotaSdk\Constants\AdditionalOperations;
use Placetopay\CamaraComercioBogotaSdk\Exceptions\ParserException;
use Placetopay\CamaraComercioBogotaSdk\Parsers\ConsultInformationParser;
use PlacetoPay\Tangram\Entities\BaseSettings;

class ParserManager
{
    protected const OPERATIONS_PARSERS = [
        AdditionalOperations::CONSULT_INFORMATION => ConsultInformationParser::class,
    ];

    protected array $parsers = [];
    protected BaseSettings $settings;

    public function __construct(BaseSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @throws ParserException
     */
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
