<?php

namespace Placetopay\CamaraComercioBogotaSdk\Simulators\Behaviours;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class ConsultInformationBehaviour extends BaseSimulatorBehaviour
{
    protected const CASES = [
        '111111111' => 'rejected',
        '222222222' => 'failed',
    ];

    public function resolve(RequestInterface $request): Response
    {
        $requestData = json_decode($request->getBody()->getContents(), true);
        $case = self::CASES[$requestData['Identificacion']] ?? 'success';

        return $this->$case();
    }

    public function success(): Response
    {
        return $this->response(200, [
            'transaction' => [
                'id' => 'txn0309328504202511030001',
                'provider' => [
                    'name' => 'CamaraDeComercio_360Kompany',
                    'id' => '4',
                    'endpoint' => '/company/get',
                    'parser' => 'kompany_variablesEmpresariales360Parser',
                ],
                'request' => [
                    'typeIdentification' => '2',
                    'identification' => '9012703752',
                    'matricula' => '03093285',
                    'rawRequestSample' => [
                        'TipoIdentificacion' => '2',
                        'Identificacion' => '9012703752',
                        'IdCamara' => '',
                        'Matricula' => '',
                        'UsuarioServicioWeb' => 'usuario',
                        'IdLlaveServicio' => 'llave',
                    ],
                ],
                'status' => [
                    'code' => 200,
                    'successful' => true,
                    'reason' => 'OK',
                    'message' => 'Consulta realizada con éxito',
                ],
                'timestamp' => '2025-11-03T12:48:00-05:00',
                'rawResponse' => [
                    'variablesEmpresariales360Kompany' => [
                        [
                            'NUR' => '0309328504',
                            'RazonSocial' => 'OPENSTOR COLOMBIA SAS',
                            'Identificacion' => '9012703752',
                            'Matricula' => '03093285',
                            'Sigla' => '',
                            'OrganizacionJuridica' => 'Sociedad por Acciones Simplificada',
                            'Categoria' => 'Principal',
                            'TipoRegimen' => 'Regimen Comun',
                            'IdCamara' => '4',
                            'CamaraC' => 'BOGOTA',
                            'FechaConstitucion' => '02/04/2019',
                            'EstadoMat' => 'Activa',
                            'FechaMatricula' => '02/04/2019',
                            'FecUltimaRenov' => '28/03/2022',
                            'Personal' => '0',
                            'SeccionCIIU1' => 'J',
                            'CIIU1' => '6202',
                            'DescripcionCIIU1' => 'ACTIVIDADES DE CONSULTORÍA INFORMÁTICA Y ACTIVIDADES DE ADMINISTRACIÓN DE INSTALACIONES INFORMÁTICAS',
                            'Direccion' => 'KR 9 # 115 - 06 PI 17',
                            'Departamento' => '11',
                            'CiudadMpio' => '11001',
                            'Telefono1' => '6398358',
                            'Telefono2' => '3508691418',
                            'Fax' => 'Sin dato',
                            'Email' => 'julian.echeverry@openstor.com.mx',
                            'ActivoTotal' => '342862366',
                            'PasivoTotal' => '135996505',
                            'PatrimonioNeto' => '206865861',
                            'IngresosAO' => '1162135950.00000',
                            'UtilPerdidaNeta' => '-84212691',
                            'InfoCertifica' => [
                                [
                                    'ID_CERTIFICA' => '40',
                                    'NOMBRE_CERTIFICA' => 'CONSTITUCION',
                                    'DESCRIPCION_CERTIFICA' => 'Constitución: Que por Documento Privado...',
                                ],
                            ],
                            'RepresentanteLegal' => [
                                [
                                    'ID_RepresentanteLegal' => 'G22096897',
                                    'RepresentanteLegal' => 'MAURICIO RICO VERDIN',
                                    'CargoRepresentante' => 'REPRESENTANTE LEGAL',
                                    'IdTipoIdentificacionRepresentante' => '5',
                                    'TipoPersonaRepresentante' => 'Persona Jurídica',
                                ],
                            ],
                        ],
                    ],
                ],
                'company' => [
                    'nur' => '0309328504',
                    'businessName' => 'OPENSTOR COLOMBIA SAS',
                    'identification' => [
                        'type' => '2',
                        'number' => '9012703752',
                    ],
                    'registry' => [
                        'matricula' => '03093285',
                        'chamber' => [
                            'id' => '4',
                            'name' => 'BOGOTA',
                        ],
                        'status' => 'Activa',
                        'registrationDate' => '2019-04-02',
                        'constitutionDate' => '2019-04-02',
                        'lastRenewalDate' => '2022-03-28',
                    ],
                    'legal' => [
                        'legalForm' => 'Sociedad por Acciones Simplificada',
                        'category' => 'Principal',
                        'taxRegime' => 'Regimen Comun',
                    ],
                    'economicActivity' => [
                        'ciiu' => [
                            'section' => 'J',
                            'code' => '6202',
                            'description' => 'ACTIVIDADES DE CONSULTORÍA INFORMÁTICA Y ACTIVIDADES DE ADMINISTRACIÓN DE INSTALACIONES INFORMÁTICAS',
                        ],
                    ],
                    'contacts' => [
                        'address' => 'KR 9 # 115 - 06 PI 17',
                        'departmentCode' => '11',
                        'cityCode' => '11001',
                        'phones' => ['6398358', '3508691418', '6398358'],
                        'fax' => null,
                        'email' => 'julian.echeverry@openstor.com.mx',
                        'website' => null,
                    ],
                    'financials' => [
                        'assets' => [
                            'current' => 317192832,
                            'nonCurrent' => 25669534,
                            'fixed' => 0,
                            'total' => 342862366,
                        ],
                        'liabilities' => [
                            'current' => 135996505,
                            'nonCurrent' => 0,
                            'total' => 135996505,
                        ],
                        'equity' => [
                            'netWorth' => 206865861,
                        ],
                        'income' => [
                            'operationalIncome' => 1162135950.0,
                            'netProfitLoss' => -84212691,
                        ],
                    ],
                    'extra' => [
                        'employeesCount' => 0,
                        'typePerson' => 'Persona Jurídica',
                        'lastRenovationYear' => 2022,
                    ],
                    'certificates' => [
                        [
                            'id' => '40',
                            'name' => 'CONSTITUCION',
                            'description' => 'Constitución: Que por Documento Privado no. sin num...',
                        ],
                        [
                            'id' => '720',
                            'name' => 'VIGENCIA',
                            'description' => 'Duración: Que la sociedad no se halla disuelta...',
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
                    ],
                ],
                'counterparties' => [
                    [
                        'role' => 'representative',
                        'name' => 'MAURICIO RICO VERDIN',
                        'identification' => 'G22096897',
                        'identificationType' => '5',
                        'source' => 'RepresentanteLegal',
                    ],
                ],
                'metadata' => [
                    'processingTimeMs' => 156,
                    'parserVersion' => '1.0.0',
                    'transactionNotes' => 'Parser adaptó campos a JSON Schema unificado (Tangram).',
                ],
            ],
        ]);
    }

    public function rejected(): Response
    {
        return $this->response(400, [
            'transaction' => [
                'id' => 'txn0309328504202511030002',
                'provider' => [
                    'name' => 'CamaraDeComercio_360Kompany',
                    'id' => '4',
                    'endpoint' => '/company/get',
                    'parser' => 'kompany_variablesEmpresariales360Parser',
                ],
                'request' => [
                    'typeIdentification' => 'X',
                    'identification' => '90A12703752',
                    'matricula' => '',
                    'rawRequestSample' => [
                        'TipoIdentificacion' => 'X',
                        'Identificacion' => '90A12703752',
                        'IdCamara' => '',
                        'Matricula' => '',
                        'UsuarioServicioWeb' => 'usuario',
                        'IdLlaveServicio' => 'llave',
                    ],
                ],
                'status' => [
                    'code' => 400,
                    'successful' => false,
                    'reason' => 'Bad Request',
                    'message' => 'Formato del request inválido: campos obligatorios con formato incorrecto',
                ],
                'timestamp' => '2025-11-03T12:55:00-05:00',
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

    public function failed(): Response
    {
        return $this->response(500, [
            'transaction' => [
                'id' => 'txn0309328504202511030003',
                'provider' => [
                    'name' => 'CamaraDeComercio_360Kompany',
                    'id' => '4',
                    'endpoint' => '/company/get',
                    'parser' => 'kompany_variablesEmpresariales360Parser',
                ],
                'request' => [
                    'typeIdentification' => '2',
                    'identification' => '9012703752',
                    'matricula' => '03093285',
                    'rawRequestSample' => [
                        'TipoIdentificacion' => '2',
                        'Identificacion' => '9012703752',
                        'IdCamara' => '',
                        'Matricula' => '03093285',
                        'UsuarioServicioWeb' => 'usuario',
                        'IdLlaveServicio' => 'llave',
                    ],
                ],
                'status' => [
                    'code' => 500,
                    'successful' => false,
                    'reason' => 'Internal Server Error',
                    'message' => 'Error interno del proveedor al procesar la solicitud',
                ],
                'timestamp' => '2025-11-03T13:05:00-05:00',
                'rawResponse' => [
                    'httpStatus' => 500,
                    'error' => [
                        'code' => 'INTERNAL_SERVER_ERROR',
                        'message' => 'Unhandled exception while processing request',
                        'traceId' => '00-4bf92f3577b34da6a3ce929d0e0e4736-00f067aa0ba902b7-00',
                        'timestamp' => '2025-11-03T13:05:00Z',
                        'hint' => 'Reintentar más tarde. Si el error persiste contacte al proveedor.',
                    ],
                    'headers' => [
                        'Retry-After' => '30',
                        'X-Request-Id' => 'req-20251103-3a9f6b',
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
                    'processingTimeMs' => 612,
                    'parserVersion' => '1.0.0',
                    'retrySuggested' => true,
                    'transactionNotes' => 'Error 500 recibido desde la Cámara de Comercio — no se intentó mapear ni persistir datos.',
                ],
            ],
        ]);
    }
}
