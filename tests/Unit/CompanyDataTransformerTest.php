<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Placetopay\CamaraComercioBogotaSdk\Support\CompanyDataTransformer;

class CompanyDataTransformerTest extends TestCase
{
    private CompanyDataTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new CompanyDataTransformer();
    }

    public function testItReturnsNullWhenCompanyDataIsEmpty(): void
    {
        $this->assertNull($this->transformer->transform(null));
        $this->assertNull($this->transformer->transform([]));
    }

    public function testItTransformsCompanyData(): void
    {
        $rawData = [
            'nur' => ' 0309328504 ',
            'businessName' => ' OPENSTOR COLOMBIA SAS ',
            'identification' => [
                'type' => ' 2 ',
                'number' => '9012703752',
            ],
            'registry' => [
                'matricula' => '03093285',
                'status' => 'Activa',
                'registrationDate' => '2019-04-02',
                'constitutionDate' => '2019-04-02',
                'lastRenewalDate' => '2022-03-28',
                'chamber' => [
                    'id' => '4',
                    'name' => ' BOGOTA ',
                ],
            ],
            'legal' => [
                'legalForm' => ' Sociedad por Acciones Simplificada ',
                'category' => 'Principal',
                'taxRegime' => 'Regimen Comun',
            ],
            'economicActivity' => [
                'ciiu' => [
                    'section' => 'J',
                    'code' => '6202',
                    'description' => 'ACTIVIDADES DE CONSULTORÍA INFORMÁTICA',
                ],
            ],
            'contacts' => [
                'address' => 'KR 9 # 115 - 06 PI 17',
                'departmentCode' => '11',
                'cityCode' => '11001',
                'phones' => ['6398358', '', '   ', null],
                'fax' => 'Sin dato',
                'email' => '  ',
                'website' => 'Sin dato',
            ],
            'financials' => [
                'assets' => [
                    'current' => '317192832',
                    'total' => '342862366',
                ],
                'liabilities' => [
                    'total' => '135996505',
                ],
                'equity' => [
                    'netWorth' => '206865861',
                ],
                'income' => [
                    'operationalIncome' => '1162135950.00000',
                    'netProfitLoss' => '-84212691',
                ],
            ],
            'extra' => [
                'employeesCount' => '0',
                'typePerson' => ' Persona Jurídica ',
                'lastRenewalYear' => '2022',
            ],
            'certificates' => [
                [
                    'id' => '40',
                    'name' => 'CONSTITUCION',
                    'description' => 'Constitución...',
                ],
                [
                    'id' => null,
                    'name' => null,
                ],
            ],
            'representatives' => [
                [
                    'id' => 'G22096897',
                    'name' => 'MAURICIO RICO VERDIN',
                    'role' => 'REPRESENTANTE LEGAL',
                    'identificationType' => '5',
                    'personType' => 'Persona Jurídica',
                ],
                null,
            ],
        ];

        $result = $this->transformer->transform($rawData);

        $this->assertNotNull($result);
        $this->assertSame('0309328504', $result['nur']);
        $this->assertSame('OPENSTOR COLOMBIA SAS', $result['businessName']);
        $this->assertSame('2', $result['identification']['type']);
        $this->assertSame('9012703752', $result['identification']['number']);
        $this->assertSame('03093285', $result['registry']['registrationNumber']);
        $this->assertSame('Activa', $result['registry']['status']);
        $this->assertSame('4', $result['registry']['chamber']['id']);
        $this->assertSame('BOGOTA', $result['registry']['chamber']['name']);
        $this->assertSame('KR 9 # 115 - 06 PI 17', $result['contacts']['address']);
        $this->assertSame(['6398358'], $result['contacts']['phones']);
        $this->assertArrayNotHasKey('email', $result['contacts']);
        $this->assertArrayNotHasKey('website', $result['contacts']);
        $this->assertSame(342862366.0, $result['financials']['assets']['total']);
        $this->assertSame(317192832.0, $result['financials']['assets']['current']);
        $this->assertSame(135996505.0, $result['financials']['liabilities']['total']);
        $this->assertSame(206865861.0, $result['financials']['equity']['netWorth']);
        $this->assertSame(1162135950.0, $result['financials']['income']['operationalIncome']);
        $this->assertSame(-84212691.0, $result['financials']['income']['netProfitLoss']);
        $this->assertSame(0.0, $result['extra']['employeesCount']);
        $this->assertSame('Persona Jurídica', $result['extra']['typePerson']);
        $this->assertSame(2022.0, $result['extra']['lastRenewalYear']);

        $this->assertCount(1, $result['certificates']);
        $this->assertSame('40', $result['certificates'][0]['id']);
        $this->assertSame('CONSTITUCION', $result['certificates'][0]['name']);

        $this->assertCount(1, $result['representatives']);
        $this->assertSame('G22096897', $result['representatives'][0]['id']);
    }

    public function testItOmitsEmptySections(): void
    {
        $rawData = [
            'nur' => '123',
            'contacts' => [
                'phones' => [null, '   ', '', 'Sin dato'],
                'email' => 'sin dato',
            ],
            'certificates' => [
                ['id' => null, 'name' => null, 'description' => null],
            ],
        ];

        $result = $this->transformer->transform($rawData);

        $this->assertSame('123', $result['nur']);
        $this->assertArrayNotHasKey('contacts', $result);
        $this->assertArrayNotHasKey('certificates', $result);
    }
}
