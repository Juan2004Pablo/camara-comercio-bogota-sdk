<?php

namespace Placetopay\CamaraComercioBogotaSdk\Support;

class DocumentTypeMapper
{
    /**
     * Map of document types to API numeric values
     * Based on API documentation: valid values are '1', '2', '3', '4', '5'.
     */
    protected const TYPE_MAP = [
        'CC' => '1',           // Cédula de Ciudadanía
        'NIT' => '2',          // Número de Identificación Tributaria
        'CE' => '3',           // Cédula de Extranjería
        'PASSPORT' => '4',     // Pasaporte
        'PA' => '4',           // Pasaporte (alternativo)
        'TI' => '5',           // Tarjeta de Identidad
        '1' => '1',            // Already numeric
        '2' => '2',            // Already numeric
        '3' => '3',            // Already numeric
        '4' => '4',            // Already numeric
        '5' => '5',            // Already numeric
    ];

    /**
     * Convert document type to API numeric format.
     *
     * @param string|null $documentType
     * @return string|null
     */
    public static function toApiFormat(?string $documentType): ?string
    {
        if ($documentType === null || $documentType === '') {
            return null;
        }

        $normalized = strtoupper(trim($documentType));

        return self::TYPE_MAP[$normalized] ?? null;
    }

    /**
     * Check if document type is valid.
     *
     * @param string|null $documentType
     * @return bool
     */
    public static function isValid(?string $documentType): bool
    {
        return self::toApiFormat($documentType) !== null;
    }
}
