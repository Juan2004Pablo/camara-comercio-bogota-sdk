<?php

namespace Placetopay\CamaraComercioBogotaSdk\Exceptions;

class ParserException extends CamaraComercioBogotaSdkException
{
    public static function invalidOperation(string $operation): self
    {
        return new self(sprintf('Operation %s is not supported', $operation));
    }
}
