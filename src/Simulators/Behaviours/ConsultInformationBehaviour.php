<?php

namespace Placetopay\CamaraComercioBogotaSdk\Simulators\Behaviours;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class ConsultInformationBehaviour extends BaseSimulatorBehaviour
{
    protected const CASES = [
        '111111111' => 'rejected',
        '222222222' => 'failed',
        '900299228' => 'evertecPlacetopay',
    ];

    public function resolve(RequestInterface $request): Response
    {
        $requestData = json_decode($request->getBody()->getContents(), true);
        $case = self::CASES[$requestData['Identificacion']] ?? 'success';

        return $this->$case($requestData);
    }

    public function success(?array $requestData = null): Response
    {
        // Extract request data
        $typeIdentification = $requestData['TipoIdentificacion'] ?? '2';
        $identification = $requestData['Identificacion'] ?? '9012703752';
        $matricula = $requestData['Matricula'] ?? $this->generateRandomMatricula();
        
        // Generate random values
        $nur = $identification; // NUR debe tener el mismo valor que el identification number
        $businessName = $this->generateRandomBusinessName();
        $legalForm = $this->generateRandomLegalForm();
        $address = $this->generateRandomAddress();
        $phone1 = $this->generateRandomPhone();
        $phone2 = $this->generateRandomPhone();
        $email = $this->generateRandomEmail($businessName);
        $constitutionDate = $this->generateRandomDate(2010, 2020);
        $registrationDate = $constitutionDate;
        $lastRenewalDate = $this->generateRandomDate(
            (int)date('Y', strtotime($constitutionDate)),
            (int)date('Y')
        );
        $ciiuSection = $this->generateRandomCIIUSection();
        $ciiuCode = $this->generateRandomCIIUCode($ciiuSection);
        $ciiuDescription = $this->generateRandomCIIUDescription($ciiuCode);
        $assetsTotal = $this->generateRandomFinancialValue(10000000, 1000000000);
        $liabilitiesTotal = $this->generateRandomFinancialValue(1000000, (int)($assetsTotal * 0.7));
        $equity = $assetsTotal - $liabilitiesTotal;
        $operationalIncome = $this->generateRandomFinancialValue(50000000, (int)($assetsTotal * 3));
        $netProfitLoss = $this->generateRandomFinancialValue((int)(-$operationalIncome * 0.2), (int)($operationalIncome * 0.3));
        
        // Generate random number of representatives (1-3)
        $representativesCount = rand(1, 3);
        $representatives = [];
        $representativesRaw = [];
        $counterparties = [];
        
        for ($i = 0; $i < $representativesCount; $i++) {
            $repName = $this->generateRandomPersonName();
            $repId = $this->generateRandomDocumentId();
            $repType = '1'; // CC
            $repRole = $i === 0 ? 'REPRESENTANTE LEGAL' : $this->generateRandomRole();
            
            $representatives[] = [
                'id' => $repId,
                'name' => $repName,
                'role' => $repRole,
                'identificationType' => $repType,
                'personType' => 'Persona Natural',
            ];
            
            $representativesRaw[] = [
                'ID_RepresentanteLegal' => $repId,
                'RepresentanteLegal' => $repName,
                'CargoRepresentante' => $repRole,
                'IdTipoIdentificacionRepresentante' => $repType,
                'TipoPersonaRepresentante' => 'Persona Natural',
            ];
            
            $counterparties[] = [
                'role' => strtolower(str_replace(' ', '_', $repRole)),
                'name' => $repName,
                'identification' => $repId,
                'identificationType' => $repType,
                'source' => 'RepresentanteLegal',
            ];
        }
        
        $transactionId = 'txn' . $nur . date('YmdHis') . rand(1000, 9999);
        $timestamp = date('c');
        $processingTime = rand(50, 500);
        
        return $this->response(200, [
            'transaction' => [
                'id' => $transactionId,
                'provider' => [
                    'name' => 'CamaraDeComercio_360Kompany',
                    'id' => '4',
                    'endpoint' => '/company/get',
                    'parser' => 'kompany_variablesEmpresariales360Parser',
                ],
                'request' => [
                    'typeIdentification' => $typeIdentification,
                    'identification' => $identification,
                    'matricula' => $matricula,
                    'rawRequestSample' => [
                        'TipoIdentificacion' => $typeIdentification,
                        'Identificacion' => $identification,
                        'IdCamara' => $requestData['IdCamara'] ?? '',
                        'Matricula' => $matricula,
                        'UsuarioServicioWeb' => $requestData['UsuarioServicioWeb'] ?? 'usuario',
                        'IdLlaveServicio' => $requestData['IdLlaveServicio'] ?? 'llave',
                    ],
                ],
                'status' => [
                    'code' => 200,
                    'successful' => true,
                    'reason' => 'OK',
                    'message' => 'Consulta realizada con éxito',
                ],
                'timestamp' => $timestamp,
                'rawResponse' => [
                    'variablesEmpresariales360Kompany' => [
                        [
                            'NUR' => $nur,
                            'RazonSocial' => $businessName,
                            'Identificacion' => $identification,
                            'Matricula' => $matricula,
                            'Sigla' => '',
                            'OrganizacionJuridica' => $legalForm,
                            'Categoria' => 'Principal',
                            'TipoRegimen' => 'Regimen Comun',
                            'IdCamara' => '4',
                            'CamaraC' => 'BOGOTA',
                            'FechaConstitucion' => date('d/m/Y', strtotime($constitutionDate)),
                            'EstadoMat' => 'Activa',
                            'FechaMatricula' => date('d/m/Y', strtotime($registrationDate)),
                            'FecUltimaRenov' => date('d/m/Y', strtotime($lastRenewalDate)),
                            'Personal' => (string)rand(0, 500),
                            'SeccionCIIU1' => $ciiuSection,
                            'CIIU1' => $ciiuCode,
                            'DescripcionCIIU1' => $ciiuDescription,
                            'Direccion' => $address,
                            'Departamento' => '11',
                            'CiudadMpio' => '11001',
                            'Telefono1' => $phone1,
                            'Telefono2' => $phone2,
                            'Fax' => rand(0, 1) ? 'Sin dato' : (string)rand(6000000, 6999999),
                            'Email' => $email,
                            'ActivoTotal' => (string)$assetsTotal,
                            'PasivoTotal' => (string)$liabilitiesTotal,
                            'PatrimonioNeto' => (string)$equity,
                            'IngresosAO' => number_format($operationalIncome, 5, '.', ''),
                            'UtilPerdidaNeta' => (string)$netProfitLoss,
                            'InfoCertifica' => [
                                [
                                    'ID_CERTIFICA' => '40',
                                    'NOMBRE_CERTIFICA' => 'CONSTITUCION',
                                    'DESCRIPCION_CERTIFICA' => $this->generateCertificateDescription('CONSTITUCION', $businessName, $constitutionDate),
                                ],
                                [
                                    'ID_CERTIFICA' => '720',
                                    'NOMBRE_CERTIFICA' => 'VIGENCIA',
                                    'DESCRIPCION_CERTIFICA' => $this->generateCertificateDescription('VIGENCIA', $businessName, $lastRenewalDate),
                                ],
                            ],
                            'RepresentanteLegal' => $representativesRaw,
                        ],
                    ],
                ],
                'company' => [
                    'nur' => $nur,
                    'businessName' => $businessName,
                    'identification' => [
                        'type' => $typeIdentification,
                        'number' => $identification,
                    ],
                    'registry' => [
                        'matricula' => $matricula,
                        'chamber' => [
                            'id' => '4',
                            'name' => 'BOGOTA',
                        ],
                        'status' => 'Activa',
                        'registrationDate' => date('Y-m-d', strtotime($registrationDate)),
                        'constitutionDate' => date('Y-m-d', strtotime($constitutionDate)),
                        'lastRenewalDate' => date('Y-m-d', strtotime($lastRenewalDate)),
                    ],
                    'legal' => [
                        'legalForm' => $legalForm,
                        'category' => 'Principal',
                        'taxRegime' => 'Regimen Comun',
                    ],
                    'economicActivity' => [
                        'ciiu' => [
                            'section' => $ciiuSection,
                            'code' => $ciiuCode,
                            'description' => $ciiuDescription,
                        ],
                    ],
                    'contacts' => [
                        'address' => $address,
                        'departmentCode' => '11',
                        'cityCode' => '11001',
                        'phones' => array_filter([$phone1, $phone2, rand(0, 1) ? $this->generateRandomPhone() : null]),
                        'fax' => null,
                        'email' => $email,
                        'website' => rand(0, 1) ? 'https://www.' . strtolower(str_replace(' ', '', $businessName)) . '.com' : null,
                    ],
                    'financials' => [
                        'assets' => [
                            'current' => (float)(int)($assetsTotal * 0.9),
                            'nonCurrent' => (float)(int)($assetsTotal * 0.1),
                            'fixed' => 0.0,
                            'total' => (float)$assetsTotal,
                        ],
                        'liabilities' => [
                            'current' => (float)(int)($liabilitiesTotal * 0.8),
                            'nonCurrent' => (float)(int)($liabilitiesTotal * 0.2),
                            'total' => (float)$liabilitiesTotal,
                        ],
                        'equity' => [
                            'netWorth' => (float)$equity,
                        ],
                        'income' => [
                            'operationalIncome' => (float)$operationalIncome,
                            'netProfitLoss' => (float)$netProfitLoss,
                        ],
                    ],
                    'extra' => [
                        'employeesCount' => (float)rand(0, 500),
                        'typePerson' => 'Persona Jurídica',
                        'lastRenovationYear' => (int)date('Y', strtotime($lastRenewalDate)),
                    ],
                    'certificates' => [
                        [
                            'id' => '40',
                            'name' => 'CONSTITUCION',
                            'description' => $this->generateCertificateDescription('CONSTITUCION', $businessName, $constitutionDate),
                        ],
                        [
                            'id' => '720',
                            'name' => 'VIGENCIA',
                            'description' => $this->generateCertificateDescription('VIGENCIA', $businessName, $lastRenewalDate),
                        ],
                    ],
                    'representatives' => $representatives,
                ],
                'counterparties' => $counterparties,
                'metadata' => [
                    'processingTimeMs' => $processingTime,
                    'parserVersion' => '1.0.0',
                    'transactionNotes' => 'Parser adaptó campos a JSON Schema unificado (Tangram).',
                ],
            ],
        ]);
    }

    protected function generateRandomMatricula(): string
    {
        return sprintf('%08d', rand(1000000, 99999999));
    }

    protected function generateRandomBusinessName(): string
    {
        $names = [
            'TECNOLOGIA', 'SERVICIOS', 'COMERCIAL', 'INDUSTRIAL', 'CONSULTORIA',
            'SOLUCIONES', 'INNOVACION', 'DIGITAL', 'GLOBAL', 'COLOMBIA',
            'BOGOTA', 'ANDINA', 'LATINA', 'AMERICA', 'SUDAMERICA'
        ];
        $types = [
            'SAS', 'LTDA', 'SA', 'SRL', 'E.U.', 'S.C.A.'
        ];
        
        $name1 = $names[array_rand($names)];
        $name2 = $names[array_rand($names)];
        $type = $types[array_rand($types)];
        
        return "$name1 $name2 $type";
    }

    protected function generateRandomLegalForm(): string
    {
        $forms = [
            'Sociedad por Acciones Simplificada',
            'Sociedad de Responsabilidad Limitada',
            'Sociedad Anónima',
            'Empresa Unipersonal',
            'Sociedad en Comandita por Acciones',
        ];
        
        return $forms[array_rand($forms)];
    }

    protected function generateRandomAddress(): string
    {
        $types = ['KR', 'CL', 'AV', 'CRA', 'DG'];
        $type = $types[array_rand($types)];
        $number1 = rand(1, 200);
        $number2 = rand(1, 200);
        $number3 = rand(1, 200);
        $suffix = rand(0, 1) ? ' - ' . rand(1, 200) : '';
        $floor = rand(0, 1) ? ' PI ' . rand(1, 30) : '';
        
        return "$type $number1 # $number2 - $number3$suffix$floor";
    }

    protected function generateRandomPhone(): string
    {
        if (rand(0, 1)) {
            // Fijo
            return (string)rand(6000000, 6999999);
        } else {
            // Celular
            return '3' . rand(0, 9) . rand(10000000, 99999999);
        }
    }

    protected function generateRandomEmail(string $businessName): string
    {
        $domains = ['gmail.com', 'hotmail.com', 'yahoo.com', 'outlook.com', 'empresa.com.co'];
        $name = strtolower(str_replace([' ', 'SAS', 'LTDA', 'SA', 'SRL', 'E.U.', 'S.C.A.'], '', $businessName));
        $name = substr($name, 0, 15);
        $domain = $domains[array_rand($domains)];
        
        return $name . '@' . $domain;
    }

    protected function generateRandomDate(int $startYear, int $endYear): string
    {
        $start = strtotime("$startYear-01-01");
        $end = strtotime("$endYear-12-31");
        $random = rand($start, $end);
        
        return date('Y-m-d', $random);
    }

    protected function generateRandomCIIUSection(): string
    {
        $sections = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];
        return $sections[array_rand($sections)];
    }

    protected function generateRandomCIIUCode(string $section): string
    {
        // Generate a 4-digit code that starts with a number related to the section
        $base = ord($section) - ord('A') + 1;
        $code = str_pad((string)($base * 100 + rand(0, 99)), 4, '0', STR_PAD_LEFT);
        
        return $code;
    }

    protected function generateRandomCIIUDescription(string $code): string
    {
        $activities = [
            'ACTIVIDADES DE CONSULTORÍA INFORMÁTICA',
            'COMERCIO AL POR MAYOR Y AL POR MENOR',
            'ACTIVIDADES DE SERVICIOS ADMINISTRATIVOS',
            'ACTIVIDADES DE CONSTRUCCIÓN',
            'ACTIVIDADES DE MANUFACTURA',
            'ACTIVIDADES DE TRANSPORTE Y ALMACENAMIENTO',
            'ACTIVIDADES DE ALOJAMIENTO Y SERVICIO DE COMIDAS',
            'ACTIVIDADES FINANCIERAS Y DE SEGUROS',
            'ACTIVIDADES INMOBILIARIAS',
            'ACTIVIDADES PROFESIONALES, CIENTÍFICAS Y TÉCNICAS',
        ];
        
        return $activities[array_rand($activities)];
    }

    protected function generateRandomFinancialValue(int $min, int $max): float
    {
        return (float)rand($min, $max);
    }

    protected function generateRandomPersonName(): string
    {
        $firstNames = [
            'CARLOS', 'MARIA', 'JUAN', 'ANA', 'LUIS', 'LAURA', 'PEDRO', 'SOFIA',
            'ANDRES', 'CAMILA', 'DIEGO', 'VALENTINA', 'FERNANDO', 'ISABELLA',
            'RICARDO', 'MARIANA', 'ALEJANDRO', 'NATALIA', 'JORGE', 'DANIELA'
        ];
        $lastNames = [
            'GARCIA', 'RODRIGUEZ', 'LOPEZ', 'MARTINEZ', 'GONZALEZ', 'PEREZ',
            'SANCHEZ', 'RAMIREZ', 'TORRES', 'FLORES', 'RIVERA', 'GOMEZ',
            'DIAZ', 'CRUZ', 'MORALES', 'ORTIZ', 'GUTIERREZ', 'CHAVEZ',
            'RUIZ', 'MENDOZA', 'HERRERA', 'SILVA', 'JIMENEZ', 'VARGAS'
        ];
        
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName1 = $lastNames[array_rand($lastNames)];
        $lastName2 = $lastNames[array_rand($lastNames)];
        
        return "$firstName $lastName1 $lastName2";
    }

    protected function generateRandomDocumentId(): string
    {
        $types = ['CC', 'CE', 'TI'];
        $type = $types[array_rand($types)];
        
        if ($type === 'CC' || $type === 'TI') {
            return (string)rand(10000000, 99999999);
        } else {
            // CE format: letter + numbers
            $letter = chr(rand(65, 90)); // A-Z
            return $letter . rand(1000000, 9999999);
        }
    }

    protected function generateRandomRole(): string
    {
        $roles = [
            'REPRESENTANTE LEGAL',
            'SUBGERENTE PRIMERO',
            'SUBGERENTE SEGUNDO',
            'GERENTE GENERAL',
            'VICEPRESIDENTE',
            'DIRECTOR',
        ];
        
        return $roles[array_rand($roles)];
    }

    protected function generateCertificateDescription(string $type, string $businessName, string $date): string
    {
        $dateFormatted = date('d/m/Y', strtotime($date));
        
        if ($type === 'CONSTITUCION') {
            $docNumber = rand(1, 9999);
            return "Constitución: Que por Documento Privado número $docNumber de fecha $dateFormatted, " .
                   "se constituyó la sociedad $businessName, con domicilio en la ciudad de Bogotá, " .
                   "conforme a las disposiciones legales vigentes. La sociedad tiene por objeto social " .
                   "el desarrollo de actividades comerciales, industriales y de servicios relacionados " .
                   "con su razón social, pudiendo realizar todos los actos y contratos necesarios " .
                   "para el cumplimiento de su objeto social.";
        } else {
            return "Vigencia: Que la sociedad $businessName, identificada con NIT, se encuentra " .
                   "vigente y en pleno funcionamiento según consta en los registros de la Cámara " .
                   "de Comercio de Bogotá. La sociedad no se halla disuelta ni en proceso de " .
                   "liquidación. La última renovación de matrícula se efectuó el día $dateFormatted, " .
                   "manteniendo su estado activo y cumpliendo con todas las obligaciones legales " .
                   "y comerciales establecidas por la normatividad vigente.";
        }
    }

    public function rejected(?array $requestData = null): Response
    {
        $typeIdentification = $requestData['TipoIdentificacion'] ?? 'X';
        $identification = $requestData['Identificacion'] ?? '90A12703752';
        
        return $this->response(400, [
            'transaction' => [
                'id' => 'txn' . date('YmdHis') . rand(1000, 9999),
                'provider' => [
                    'name' => 'CamaraDeComercio_360Kompany',
                    'id' => '4',
                    'endpoint' => '/company/get',
                    'parser' => 'kompany_variablesEmpresariales360Parser',
                ],
                'request' => [
                    'typeIdentification' => $typeIdentification,
                    'identification' => $identification,
                    'matricula' => $requestData['Matricula'] ?? '',
                    'rawRequestSample' => [
                        'TipoIdentificacion' => $typeIdentification,
                        'Identificacion' => $identification,
                        'IdCamara' => $requestData['IdCamara'] ?? '',
                        'Matricula' => $requestData['Matricula'] ?? '',
                        'UsuarioServicioWeb' => $requestData['UsuarioServicioWeb'] ?? 'usuario',
                        'IdLlaveServicio' => $requestData['IdLlaveServicio'] ?? 'llave',
                    ],
                ],
                'status' => [
                    'code' => 400,
                    'successful' => false,
                    'reason' => 'Bad Request',
                    'message' => 'Formato del request inválido: campos obligatorios con formato incorrecto',
                ],
                'timestamp' => date('c'),
                'rawResponse' => [
                    'error' => [
                        'code' => 'INVALID_PAYLOAD',
                        'httpStatus' => 400,
                        'message' => "El campo 'Identificacion' contiene caracteres no numéricos. Tipo de identificacion 'X' no válido.",
                        'details' => [
                            [
                                'field' => 'Identificacion',
                                'issue' => 'Formato inválido: se esperaba solo dígitos.',
                            ],
                            [
                                'field' => 'TipoIdentificacion',
                                'issue' => "Valor no reconocido. Valores válidos: '1', '2', '3', '4', '5'.",
                            ],
                        ],
                    ],
                ],
                'company' => null,
                'counterparties' => [],
                'errors' => [
                    [
                        'code' => 'VALIDATION_ERROR',
                        'message' => 'Payload validation failed',
                        'validationErrors' => [
                            [
                                'path' => '/request/typeIdentification',
                                'message' => "Tipo de identificación inválido: 'X'",
                            ],
                            [
                                'path' => '/request/identification',
                                'message' => "Identificación contiene caracteres no permitidos: '90A12703752'",
                            ],
                        ],
                    ],
                ],
                'metadata' => [
                    'processingTimeMs' => 48,
                    'parserVersion' => '1.0.0',
                    'transactionNotes' => 'Fallo en validación previa al mapping. No se intentó llamar al mapeador por payload inválido.',
                ],
            ],
        ]);
    }

    public function evertecPlacetopay(?array $requestData = null): Response
    {
        $typeIdentification = $requestData['TipoIdentificacion'] ?? '2';
        $identification = '900299228';
        $nur = $identification;
        $businessName = 'EVERTEC PLACETOPAY SAS';
        $matricula = '41652612';
        $constitutionDate = '2009-07-09';
        $registrationDate = '2009-07-09';
        $lastRenewalDate = '2023-03-29';
        $timestamp = date('c');
        $transactionId = 'txn' . $nur . date('YmdHis') . rand(1000, 9999);
        $processingTime = rand(50, 500);
        
        // Economic activities from the data
        $economicActivities = [
            [
                'CodActividad' => '6311',
                'Descripcion' => 'PROCESAMIENTO DE DATOS, ALOJAMIENTO (HOSTING) Y ACTIVIDADES RELACIONADAS'
            ],
            [
                'CodActividad' => '6201',
                'Descripcion' => 'ACTIVIDADES DE DESARROLLO DE SISTEMAS INFORMATICOS (PLANIFICACION, ANALISIS, DISEÑO, PROGRAMACION, PRUEBAS)'
            ],
            [
                'CodActividad' => '6209',
                'Descripcion' => 'OTRAS ACTIVIDADES DE TECNOLOGIAS DE INFORMACION Y ACTIVIDADES DE SERVICIOS INFORMATICOS'
            ]
        ];
        
        // Legal representatives from the data
        $representativesRaw = [
            [
                'ID_RepresentanteLegal' => '71310533',
                'RepresentanteLegal' => 'RICARDO GARCIA MOLINA',
                'CargoRepresentante' => 'REPRESENTANTE LEGAL',
                'IdTipoIdentificacionRepresentante' => '1',
                'TipoPersonaRepresentante' => 'Persona Natural',
            ],
            [
                'ID_RepresentanteLegal' => '520998310',
                'RepresentanteLegal' => 'GUILLERMO ADOLFO PAS ROSPIGLIOSI',
                'CargoRepresentante' => 'SUBGERENTE SEGUNDO',
                'IdTipoIdentificacionRepresentante' => '1',
                'TipoPersonaRepresentante' => 'Persona Natural',
            ],
            [
                'ID_RepresentanteLegal' => '874285',
                'RepresentanteLegal' => 'ALBA MARIA CAMPOS MONGE',
                'CargoRepresentante' => 'REPRESENTANTE LEGAL',
                'IdTipoIdentificacionRepresentante' => '1',
                'TipoPersonaRepresentante' => 'Persona Natural',
            ],
        ];
        
        $representatives = [
            [
                'id' => '71310533',
                'name' => 'RICARDO GARCIA MOLINA',
                'role' => 'REPRESENTANTE LEGAL',
                'identificationType' => '1',
                'personType' => 'Persona Natural',
            ],
            [
                'id' => '520998310',
                'name' => 'GUILLERMO ADOLFO PAS ROSPIGLIOSI',
                'role' => 'SUBGERENTE SEGUNDO',
                'identificationType' => '1',
                'personType' => 'Persona Natural',
            ],
            [
                'id' => '874285',
                'name' => 'ALBA MARIA CAMPOS MONGE',
                'role' => 'REPRESENTANTE LEGAL',
                'identificationType' => '1',
                'personType' => 'Persona Natural',
            ],
        ];
        
        $counterparties = [
            [
                'role' => 'representative',
                'name' => 'RICARDO GARCIA MOLINA',
                'identification' => '71310533',
                'identificationType' => '1',
                'source' => 'RepresentanteLegal',
            ],
            [
                'role' => 'subgerente_segundo',
                'name' => 'GUILLERMO ADOLFO PAS ROSPIGLIOSI',
                'identification' => '520998310',
                'identificationType' => '1',
                'source' => 'RepresentanteLegal',
            ],
            [
                'role' => 'representative',
                'name' => 'ALBA MARIA CAMPOS MONGE',
                'identification' => '874285',
                'identificationType' => '1',
                'source' => 'RepresentanteLegal',
            ],
        ];
        
        // Generate some random financial values
        $assetsTotal = $this->generateRandomFinancialValue(500000000, 2000000000);
        $liabilitiesTotal = $this->generateRandomFinancialValue(100000000, (int)($assetsTotal * 0.6));
        $equity = $assetsTotal - $liabilitiesTotal;
        $operationalIncome = $this->generateRandomFinancialValue(1000000000, (int)($assetsTotal * 4));
        $netProfitLoss = $this->generateRandomFinancialValue((int)(-$operationalIncome * 0.15), (int)($operationalIncome * 0.25));
        
        return $this->response(200, [
            'transaction' => [
                'id' => $transactionId,
                'provider' => [
                    'name' => 'CamaraDeComercio_360Kompany',
                    'id' => '21',
                    'endpoint' => '/company/get',
                    'parser' => 'kompany_variablesEmpresariales360Parser',
                ],
                'request' => [
                    'typeIdentification' => $typeIdentification,
                    'identification' => $identification,
                    'matricula' => $matricula,
                    'rawRequestSample' => [
                        'TipoIdentificacion' => $typeIdentification,
                        'Identificacion' => $identification,
                        'IdCamara' => $requestData['IdCamara'] ?? '',
                        'Matricula' => $matricula,
                        'UsuarioServicioWeb' => $requestData['UsuarioServicioWeb'] ?? 'usuario',
                        'IdLlaveServicio' => $requestData['IdLlaveServicio'] ?? 'llave',
                    ],
                ],
                'status' => [
                    'code' => 200,
                    'successful' => true,
                    'reason' => 'OK',
                    'message' => 'Consulta realizada con éxito',
                ],
                'timestamp' => $timestamp,
                'rawResponse' => [
                    'variablesEmpresariales360Kompany' => [
                        [
                            'NUR' => $nur,
                            'RazonSocial' => $businessName,
                            'Identificacion' => $identification,
                            'Matricula' => $matricula,
                            'Sigla' => '',
                            'OrganizacionJuridica' => 'SOCIEDADES POR ACCIONES SIMPLIFICADAS SAS',
                            'Categoria' => 'SOCIEDAD ó PERSONA JURIDICA PRINCIPAL ó ESAL',
                            'TipoRegimen' => 'Regimen Comun',
                            'IdCamara' => '21',
                            'CamaraC' => 'MEDELLIN PARA ANTIOQUIA',
                            'FechaConstitucion' => date('d/m/Y', strtotime($constitutionDate)),
                            'EstadoMat' => 'ACTIVA',
                            'FechaMatricula' => date('d/m/Y', strtotime($registrationDate)),
                            'FecUltimaRenov' => date('d/m/Y', strtotime($lastRenewalDate)),
                            'Personal' => (string)rand(50, 500),
                            'SeccionCIIU1' => 'J',
                            'CIIU1' => '6311',
                            'DescripcionCIIU1' => $economicActivities[0]['Descripcion'],
                            'Direccion' => $this->generateRandomAddress(),
                            'Departamento' => '05',
                            'CiudadMpio' => '05001',
                            'Telefono1' => $this->generateRandomPhone(),
                            'Telefono2' => $this->generateRandomPhone(),
                            'Fax' => rand(0, 1) ? 'Sin dato' : (string)rand(6000000, 6999999),
                            'Email' => $this->generateRandomEmail($businessName),
                            'ActivoTotal' => (string)$assetsTotal,
                            'PasivoTotal' => (string)$liabilitiesTotal,
                            'PatrimonioNeto' => (string)$equity,
                            'IngresosAO' => number_format($operationalIncome, 5, '.', ''),
                            'UtilPerdidaNeta' => (string)$netProfitLoss,
                            'InfoCertifica' => [
                                [
                                    'ID_CERTIFICA' => '40',
                                    'NOMBRE_CERTIFICA' => 'CONSTITUCION',
                                    'DESCRIPCION_CERTIFICA' => $this->generateCertificateDescription('CONSTITUCION', $businessName, $constitutionDate),
                                ],
                                [
                                    'ID_CERTIFICA' => '720',
                                    'NOMBRE_CERTIFICA' => 'VIGENCIA',
                                    'DESCRIPCION_CERTIFICA' => $this->generateCertificateDescription('VIGENCIA', $businessName, $lastRenewalDate),
                                ],
                            ],
                            'RepresentanteLegal' => $representativesRaw,
                        ],
                    ],
                ],
                'company' => [
                    'nur' => $nur,
                    'businessName' => $businessName,
                    'identification' => [
                        'type' => $typeIdentification,
                        'number' => $identification,
                    ],
                    'registry' => [
                        'matricula' => $matricula,
                        'chamber' => [
                            'id' => '21',
                            'name' => 'MEDELLIN PARA ANTIOQUIA',
                        ],
                        'status' => 'ACTIVA',
                        'registrationDate' => $registrationDate,
                        'constitutionDate' => $constitutionDate,
                        'lastRenewalDate' => $lastRenewalDate,
                    ],
                    'legal' => [
                        'legalForm' => 'SOCIEDADES POR ACCIONES SIMPLIFICADAS SAS',
                        'category' => 'SOCIEDAD ó PERSONA JURIDICA PRINCIPAL ó ESAL',
                        'taxRegime' => 'Regimen Comun',
                    ],
                    'economicActivity' => [
                        'ciiu' => [
                            'section' => 'J',
                            'code' => '6311',
                            'description' => $economicActivities[0]['Descripcion'],
                        ],
                    ],
                    'contacts' => [
                        'address' => $this->generateRandomAddress(),
                        'departmentCode' => '05',
                        'cityCode' => '05001',
                        'phones' => array_filter([$this->generateRandomPhone(), $this->generateRandomPhone(), rand(0, 1) ? $this->generateRandomPhone() : null]),
                        'fax' => null,
                        'email' => $this->generateRandomEmail($businessName),
                        'website' => rand(0, 1) ? 'https://www.' . strtolower(str_replace(' ', '', $businessName)) . '.com' : null,
                    ],
                    'financials' => [
                        'assets' => [
                            'current' => (float)(int)($assetsTotal * 0.9),
                            'nonCurrent' => (float)(int)($assetsTotal * 0.1),
                            'fixed' => 0.0,
                            'total' => (float)$assetsTotal,
                        ],
                        'liabilities' => [
                            'current' => (float)(int)($liabilitiesTotal * 0.8),
                            'nonCurrent' => (float)(int)($liabilitiesTotal * 0.2),
                            'total' => (float)$liabilitiesTotal,
                        ],
                        'equity' => [
                            'netWorth' => (float)$equity,
                        ],
                        'income' => [
                            'operationalIncome' => (float)$operationalIncome,
                            'netProfitLoss' => (float)$netProfitLoss,
                        ],
                    ],
                    'extra' => [
                        'employeesCount' => (float)rand(50, 500),
                        'typePerson' => 'Persona Jurídica',
                        'lastRenovationYear' => 2023,
                    ],
                    'certificates' => [
                        [
                            'id' => '40',
                            'name' => 'CONSTITUCION',
                            'description' => $this->generateCertificateDescription('CONSTITUCION', $businessName, $constitutionDate),
                        ],
                        [
                            'id' => '720',
                            'name' => 'VIGENCIA',
                            'description' => $this->generateCertificateDescription('VIGENCIA', $businessName, $lastRenewalDate),
                        ],
                    ],
                    'representatives' => $representatives,
                ],
                'counterparties' => $counterparties,
                'metadata' => [
                    'processingTimeMs' => $processingTime,
                    'parserVersion' => '1.0.0',
                    'transactionNotes' => 'Parser adaptó campos a JSON Schema unificado (Tangram).',
                ],
            ],
        ]);
    }

    public function failed(?array $requestData = null): Response
    {
        $typeIdentification = $requestData['TipoIdentificacion'] ?? '2';
        $identification = $requestData['Identificacion'] ?? '9012703752';
        $matricula = $requestData['Matricula'] ?? '';
        
        return $this->response(500, [
            'transaction' => [
                'id' => 'txn' . date('YmdHis') . rand(1000, 9999),
                'provider' => [
                    'name' => 'CamaraDeComercio_360Kompany',
                    'id' => '4',
                    'endpoint' => '/company/get',
                    'parser' => 'kompany_variablesEmpresariales360Parser',
                ],
                'request' => [
                    'typeIdentification' => $typeIdentification,
                    'identification' => $identification,
                    'matricula' => $matricula,
                    'rawRequestSample' => [
                        'TipoIdentificacion' => $typeIdentification,
                        'Identificacion' => $identification,
                        'IdCamara' => $requestData['IdCamara'] ?? '',
                        'Matricula' => $matricula,
                        'UsuarioServicioWeb' => $requestData['UsuarioServicioWeb'] ?? 'usuario',
                        'IdLlaveServicio' => $requestData['IdLlaveServicio'] ?? 'llave',
                    ],
                ],
                'status' => [
                    'code' => 500,
                    'successful' => false,
                    'reason' => 'Internal Server Error',
                    'message' => 'Error interno del proveedor al procesar la solicitud',
                ],
                'timestamp' => date('c'),
                'rawResponse' => [
                    'httpStatus' => 500,
                    'error' => [
                        'code' => 'INTERNAL_SERVER_ERROR',
                        'message' => 'Unhandled exception while processing request',
                        'traceId' => '00-4bf92f3577b34da6a3ce929d0e0e4736-00f067aa0ba902b7-00',
                        'timestamp' => date('c'),
                        'hint' => 'Reintentar más tarde. Si el error persiste contacte al proveedor.',
                    ],
                    'headers' => [
                        'Retry-After' => '30',
                        'X-Request-Id' => 'req-' . date('Ymd') . '-' . substr(md5(rand()), 0, 6),
                    ],
                ],
                'company' => null,
                'counterparties' => [],
                'errors' => [
                    [
                        'code' => 'PROVIDER_ERROR',
                        'message' => 'El proveedor devolvió un error 500',
                        'details' => [
                            [
                                'field' => null,
                                'issue' => 'Error no esperado en servicio externo',
                            ],
                        ],
                        'correlation' => [
                            'providerTraceId' => '00-4bf92f3577b34da6a3ce929d0e0e4736-00f067aa0ba902b7-00',
                            'gatewayTransactionId' => 'txn0003',
                        ],
                    ],
                ],
                'metadata' => [
                    'processingTimeMs' => rand(500, 1000),
                    'parserVersion' => '1.0.0',
                    'retrySuggested' => true,
                    'transactionNotes' => 'Error 500 recibido desde la Cámara de Comercio — no se intentó mapear ni persistir datos.',
                ],
            ],
        ]);
    }
}
