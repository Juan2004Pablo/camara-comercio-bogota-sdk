<?php

namespace Tests\Feature;

use PlacetoPay\Base\Entities\Person;
use Placetopay\CamaraComercioBogotaSdk\Entities\ConsultInformationTransaction;
use Placetopay\CamaraComercioBogotaSdk\Exceptions\CamaraComercioBogotaSdkException;
use Tests\TestCase;

class ConsultInformationTest extends TestCase
{
    public function testItHandlesSuccessfulConsultInformationWithCC()
    {
        $transaction = new ConsultInformationTransaction([
            'person' => new Person([
                'document' => '9012703752',
                'documentType' => 'CC',
            ]),
        ]);

        $response = $this->createGateway()->consultInformation($transaction);

        $this->assertInstanceOf(ConsultInformationTransaction::class, $response);
        $this->assertTrue($response->status()->isSuccessful());
        $this->assertNotNull($response->company());

        $company = $response->company();
        $this->assertArrayHasKey('businessName', $company);
        $this->assertArrayHasKey('identification', $company);
        $this->assertArrayHasKey('registry', $company);
        $this->assertArrayHasKey('legal', $company);
        $this->assertArrayHasKey('contacts', $company);
        $this->assertArrayHasKey('financials', $company);
    }

    public function testItHandlesSuccessfulConsultInformationWithNIT()
    {
        $transaction = new ConsultInformationTransaction([
            'person' => new Person([
                'document' => '9012703752',
                'documentType' => 'NIT',
            ]),
        ]);

        $response = $this->createGateway()->consultInformation($transaction);

        $this->assertInstanceOf(ConsultInformationTransaction::class, $response);
        $this->assertTrue($response->status()->isSuccessful());
        $this->assertNotNull($response->company());
    }

    public function testItHandlesSuccessfulConsultInformationWithCE()
    {
        $transaction = new ConsultInformationTransaction([
            'person' => new Person([
                'document' => '9012703752',
                'documentType' => 'CE',
            ]),
        ]);

        $response = $this->createGateway()->consultInformation($transaction);

        $this->assertInstanceOf(ConsultInformationTransaction::class, $response);
        $this->assertTrue($response->status()->isSuccessful());
    }

    public function testItHandlesSuccessfulConsultInformationWithNumericDocumentType()
    {
        $transaction = new ConsultInformationTransaction([
            'person' => new Person([
                'document' => '9012703752',
                'documentType' => '2',
            ]),
        ]);

        $response = $this->createGateway()->consultInformation($transaction);

        $this->assertInstanceOf(ConsultInformationTransaction::class, $response);
        $this->assertTrue($response->status()->isSuccessful());
    }

    public function testItTransformsCompanyDataCorrectly()
    {
        $transaction = new ConsultInformationTransaction([
            'person' => new Person([
                'document' => '9012703752',
                'documentType' => 'CC',
            ]),
        ]);

        $response = $this->createGateway()->consultInformation($transaction);

        $company = $response->company();

        // Verify basic information
        $this->assertArrayHasKey('nur', $company);
        $this->assertArrayHasKey('businessName', $company);

        // Verify identification structure
        $this->assertArrayHasKey('identification', $company);
        $this->assertArrayHasKey('type', $company['identification']);
        $this->assertArrayHasKey('number', $company['identification']);

        // Verify registry structure
        $this->assertArrayHasKey('registry', $company);
        $this->assertArrayHasKey('registrationNumber', $company['registry']);
        $this->assertArrayHasKey('status', $company['registry']);
        $this->assertArrayHasKey('chamber', $company['registry']);

        // Verify contacts structure
        $this->assertArrayHasKey('contacts', $company);
        $this->assertArrayHasKey('address', $company['contacts']);
        $this->assertArrayHasKey('phones', $company['contacts']);

        // Verify financials structure
        $this->assertArrayHasKey('financials', $company);
        $this->assertArrayHasKey('assets', $company['financials']);
        $this->assertArrayHasKey('liabilities', $company['financials']);
        $this->assertArrayHasKey('equity', $company['financials']);
        $this->assertArrayHasKey('income', $company['financials']);
    }

    public function testItHandlesRejectedRequest()
    {
        $transaction = new ConsultInformationTransaction([
            'person' => new Person([
                'document' => '111111111',
                'documentType' => 'CC',
            ]),
        ]);

        $response = $this->createGateway()->consultInformation($transaction);

        $this->assertInstanceOf(ConsultInformationTransaction::class, $response);
        $this->assertFalse($response->status()->isSuccessful());
        $this->assertNull($response->company());
    }

    public function testItHandlesFailedRequest()
    {
        $transaction = new ConsultInformationTransaction([
            'person' => new Person([
                'document' => '222222222',
                'documentType' => 'CC',
            ]),
        ]);

        $response = $this->createGateway()->consultInformation($transaction);

        $this->assertInstanceOf(ConsultInformationTransaction::class, $response);
        $this->assertFalse($response->status()->isSuccessful());
        $this->assertNull($response->company());
    }

    public function testItThrowsExceptionForInvalidDocumentType()
    {
        $this->expectException(CamaraComercioBogotaSdkException::class);
        $this->expectExceptionMessage('Invalid document type: INVALID');

        $transaction = new ConsultInformationTransaction([
            'person' => new Person([
                'document' => '9012703752',
                'documentType' => 'INVALID',
            ]),
        ]);

        $this->createGateway()->consultInformation($transaction);
    }

    public function testItFiltersEmptyAndNullValues()
    {
        $transaction = new ConsultInformationTransaction([
            'person' => new Person([
                'document' => '9012703752',
                'documentType' => 'CC',
            ]),
        ]);

        $response = $this->createGateway()->consultInformation($transaction);
        $company = $response->company();

        // Verify that null/empty values are not present in contacts
        if (isset($company['contacts']['fax'])) {
            $this->assertNotNull($company['contacts']['fax']);
        }

        // Verify phones array doesn't contain null or empty values
        if (isset($company['contacts']['phones'])) {
            foreach ($company['contacts']['phones'] as $phone) {
                $this->assertNotNull($phone);
                $this->assertNotEmpty($phone);
            }
        }
    }

    public function testItConvertsNumericFinancialValues()
    {
        $transaction = new ConsultInformationTransaction([
            'person' => new Person([
                'document' => '9012703752',
                'documentType' => 'CC',
            ]),
        ]);

        $response = $this->createGateway()->consultInformation($transaction);
        $company = $response->company();

        if (isset($company['financials']['assets']['total'])) {
            $this->assertIsFloat($company['financials']['assets']['total']);
        }

        if (isset($company['financials']['liabilities']['total'])) {
            $this->assertIsFloat($company['financials']['liabilities']['total']);
        }

        if (isset($company['financials']['equity']['netWorth'])) {
            $this->assertIsFloat($company['financials']['equity']['netWorth']);
        }
    }

    public function testItIncludesCertificatesWhenPresent()
    {
        $transaction = new ConsultInformationTransaction([
            'person' => new Person([
                'document' => '9012703752',
                'documentType' => 'CC',
            ]),
        ]);

        $response = $this->createGateway()->consultInformation($transaction);
        $company = $response->company();

        if (isset($company['certificates'])) {
            $this->assertIsArray($company['certificates']);
            foreach ($company['certificates'] as $certificate) {
                $this->assertArrayHasKey('id', $certificate);
                $this->assertArrayHasKey('name', $certificate);
            }
        }
    }

    public function testItIncludesRepresentativesWhenPresent()
    {
        $transaction = new ConsultInformationTransaction([
            'person' => new Person([
                'document' => '9012703752',
                'documentType' => 'CC',
            ]),
        ]);

        $response = $this->createGateway()->consultInformation($transaction);
        $company = $response->company();

        if (isset($company['representatives'])) {
            $this->assertIsArray($company['representatives']);
            foreach ($company['representatives'] as $representative) {
                $this->assertArrayHasKey('id', $representative);
                $this->assertArrayHasKey('name', $representative);
                $this->assertArrayHasKey('role', $representative);
            }
        }
    }
}
