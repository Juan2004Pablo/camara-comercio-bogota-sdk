<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Placetopay\CamaraComercioBogotaSdk\Support\DocumentTypeMapper;

class DocumentTypeMapperTest extends TestCase
{
    public function testItMapsAlphabeticDocumentTypesToNumericValues(): void
    {
        $this->assertSame('1', DocumentTypeMapper::toApiFormat('cc'));
        $this->assertSame('2', DocumentTypeMapper::toApiFormat('NIT'));
        $this->assertSame('3', DocumentTypeMapper::toApiFormat('ce'));
        $this->assertSame('4', DocumentTypeMapper::toApiFormat('passport'));
        $this->assertSame('5', DocumentTypeMapper::toApiFormat('ti'));
    }

    public function testItKeepsNumericDocumentTypes(): void
    {
        $this->assertSame('1', DocumentTypeMapper::toApiFormat('1'));
        $this->assertSame('2', DocumentTypeMapper::toApiFormat('2'));
        $this->assertSame('3', DocumentTypeMapper::toApiFormat('3'));
        $this->assertSame('4', DocumentTypeMapper::toApiFormat('4'));
        $this->assertSame('5', DocumentTypeMapper::toApiFormat('5'));
    }

    public function testItReturnsNullForUnknownDocumentTypes(): void
    {
        $this->assertNull(DocumentTypeMapper::toApiFormat('UNKNOWN'));
        $this->assertNull(DocumentTypeMapper::toApiFormat(null));
        $this->assertNull(DocumentTypeMapper::toApiFormat(''));
        $this->assertNull(DocumentTypeMapper::toApiFormat('   '));
    }

    public function testItValidatesKnownDocumentTypes(): void
    {
        $this->assertTrue(DocumentTypeMapper::isValid('CC'));
        $this->assertTrue(DocumentTypeMapper::isValid('nit'));
        $this->assertTrue(DocumentTypeMapper::isValid('4'));

        $this->assertFalse(DocumentTypeMapper::isValid(''));
        $this->assertFalse(DocumentTypeMapper::isValid('ABC'));
    }
}
