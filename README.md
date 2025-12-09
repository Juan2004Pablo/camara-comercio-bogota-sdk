# Camara Comercio Bogota SDK

SDK PHP para consultar información empresarial en la Cámara de Comercio de Bogotá utilizando los servicios de PlacetoPay.

## Instalación

Agrega el repositorio privado de PlacetoPay a tu `composer.json`:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://dev.placetopay.com/repository"
        }
    ]
}
```

Instala el paquete:

```bash
composer require placetopay/camara-comercio-bogota-sdk
```

## Configuración Básica

```php
use PlacetoPay\Base\Entities\Person;
use Placetopay\CamaraComercioBogotaSdk\Gateway;
use Placetopay\CamaraComercioBogotaSdk\Entities\ConsultInformationTransaction;

$gateway = new Gateway([
    'username' => 'your-service-username',
    'password' => 'your-service-password',
    'url' => 'https://api.camaracomercio.gov.co',
    'simulatorMode' => false, // Habilítalo durante el desarrollo
]);

$transaction = new ConsultInformationTransaction([
    'person' => new Person([
        'document' => '9012703752',
        'documentType' => 'NIT', // Ver mapeo de tipos de documento
    ]),
]);

$response = $gateway->consultInformation($transaction);

if ($response->status()->isSuccessful()) {
    $company = $response->company(); // Información ya normalizada y filtrada
}
```

## Mapeo de tipos de documento

El API de la Cámara de Comercio espera valores numéricos para el tipo de identificación. El SDK incluye el `DocumentTypeMapper` para hacer esta conversión automáticamente.

| Tipo entrada      | Valor enviado al API |
|-------------------|----------------------|
| `CC`              | `1`                  |
| `NIT`             | `2`                  |
| `CE`              | `3`                  |
| `PASSPORT` / `PA` | `4`                  |
| `TI`              | `5`                  |
| `1` - `5`         | Se envían tal cual   |

Cualquier otro valor generará una excepción antes de hacer la petición.

## Transformación de la respuesta

El SDK estandariza la respuesta del servicio externo mediante `CompanyDataTransformer`:

- Normaliza y limpia los campos (`trim`, remueve valores como `Sin dato` y entradas vacías).
- Traduce claves a inglés y agrupa secciones (`identification`, `registry`, `legal`, `contacts`, `financials`, etc.).
- Convierte los valores numéricos a `float`.
- Elimina secciones vacías para entregar un objeto limpio y fácil de consumir.

## Pruebas

Se incluye una suite de pruebas que cubre:

- Flujo completo de consulta (`tests/Feature/ConsultInformationTest.php`).
- Resolución de configuración (`tests/Unit/SettingsResolverTest.php`).
- Clases auxiliares (`DocumentTypeMapper` y `CompanyDataTransformer`).

Ejecuta todas las pruebas con:

```bash
composer test
```

o directamente:

```bash
vendor/bin/phpunit
```