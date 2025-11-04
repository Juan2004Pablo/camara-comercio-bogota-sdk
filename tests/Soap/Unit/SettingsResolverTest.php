<?php

namespace Tests\Soap\Unit;

use DummyNamespace\Soap\Support\SettingsResolver;
use PHPUnit\Framework\TestCase;
use Placetopay\SoapClient\Client;
use PlacetoPay\Tangram\Mock\TestLogger;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class SettingsResolverTest extends TestCase
{
    public function testItDefinesADefaultProviderName()
    {
        $resolver = SettingsResolver::create([]);

        $this->assertEquals('Dummy', $resolver->resolve([])['providerName']);
    }

    public function testItValidatesProviderNameIsAString()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "providerName" with value array is expected to be of type "string", but is of type "array".'
        );

        $data = ['providerName' => ['Array']];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItDefinesSimulatorModeByDefaultAsFalse()
    {
        $resolver = SettingsResolver::create([]);

        $this->assertFalse($resolver->resolve([])['simulatorMode']);
    }

    public function testItValidatesSimulatorModeIsBoolean()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "simulatorMode" with value "falsy" is expected to be of type "bool", but is of type "string".'
        );

        $data = ['simulatorMode' => 'falsy'];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItValidatesLoggerIsAnArray()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The nested option "logger" with value "logger" is expected to be of type array, but is of type "string".'
        );

        $data = ['logger' => 'logger'];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItValidatesLoggerNameIsAString()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "logger[name]" with value array is expected to be of type "string", but is of type "array".'
        );

        $data = [
            'logger' => array_replace(
                $this->validLoggerSettings(),
                ['name' => ['array name']]
            ),
        ];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItValidatesLoggerViaIsRequired()
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "logger[via]" is missing.');

        $loggerSetting = $this->validLoggerSettings();
        unset($loggerSetting['via']);

        $data = ['logger' => $loggerSetting];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItValidatesLoggerViaImplementsLoggerInterface()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "logger[via]" with value "string via" is expected to be of type ' .
            '"Psr\Log\LoggerInterface", but is of type "string".'
        );

        $data = [
            'logger' => array_replace(
                $this->validLoggerSettings(),
                ['via' => 'string via'],
            ),
        ];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItValidatesLoggerPathIsAString()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "logger[path]" with value array is expected to be of type "string" or ' .
            '"null", but is of type "array".'
        );

        $data = [
            'logger' => array_replace(
                $this->validLoggerSettings(),
                ['path' => ['array path']]
            ),
        ];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItDefinesLoggerDebugByDefaultAsFalse()
    {
        $data = ['logger' => $this->validLoggerSettings()];

        $resolver = SettingsResolver::create($data);

        $this->assertFalse($resolver->resolve($data)['logger']['debug']);
    }

    public function testItValidatesLoggerDebugIsABoolean()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "logger[debug]" with value "falsy" is expected to be of type "bool", but is of type "string".'
        );

        $data = [
            'logger' => array_replace(
                $this->validLoggerSettings(),
                ['debug' => 'falsy']
            ),
        ];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItReceiveWsdlAsArray()
    {
        $data = ['wsdl' => ['array']];
        $resolved = SettingsResolver::create($data)->resolve($data);

        $this->assertSame(['array'], $resolved['wsdl']);
    }

    public function testItReceiveWsdlAsString()
    {
        $data = ['wsdl' => 'string'];
        $resolved = SettingsResolver::create($data)->resolve($data);

        $this->assertSame('string', $resolved['wsdl']);
    }

    public function testItsWsdlMustBeStringOrArray()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "wsdl" with value 0 is expected to be of type "string" or "string[]", but is of type "int".'
        );

        $data = ['wsdl' => 0];

        SettingsResolver::create($data)->resolve($data);
    }

    public function testItReceiveLocationsAsArray()
    {
        $data = ['locations' => ['array']];
        $resolved = SettingsResolver::create($data)->resolve($data);

        $this->assertSame(['array'], $resolved['locations']);
    }

    public function testItReceiveLocationsAsString()
    {
        $data = ['locations' => 'string'];

        $resolved = SettingsResolver::create($data)->resolve($data);

        $this->assertSame('string', $resolved['locations']);
    }

    public function testItsLocationsMustBeStringOrArray()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "locations" with value 0 is expected to be of type "string" or ' .
            '"string[]", but is of type "int".'
        );

        $data = ['locations' => 0];

        SettingsResolver::create($data)->resolve($data);
    }

    public function testItCanReceiveClientOptions()
    {
        $data = array_merge([], [
            'clientOptions' => [
                'soapVersion' => 2,
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'trace' => true,
                'encoding' => 'UTF-8',
                'connection_timeout' => 11,
            ],
        ]);

        $resolver = SettingsResolver::create($data);

        $this->assertEquals($data['clientOptions'], $resolver->resolve($data)['clientOptions']);
    }

    public function testItValidatesClientOptionsSoapVersionIsAnInteger()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "clientOptions[soapVersion]" with value "1" is expected to be of type "int", ' .
            'but is of type "string".'
        );

        $data = ['clientOptions' => array_replace(
            $this->validClientOptionsSettings(),
            [
            'soapVersion' => '1',
            ]
        )];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItValidatesClientOptionsFeaturesIsAnInteger()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "clientOptions[features]" with value "1" is expected to be of type "int", ' .
            'but is of type "string".'
        );

        $data = ['clientOptions' => array_replace(
            $this->validClientOptionsSettings(),
            [
            'features' => '1',
            ]
        )];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItValidatesClientOptionsCacheWsdlIsAnInteger()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "clientOptions[cache_wsdl]" with value "1" is expected to be of type "int", ' .
            'but is of type "string".'
        );

        $data = ['clientOptions' => array_replace(
            $this->validClientOptionsSettings(),
            [
            'cache_wsdl' => '1',
            ]
        )];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItValidatesClientOptionsTraceIsABoolean()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "clientOptions[trace]" with value "falsy" is expected to be of type "bool", ' .
            'but is of type "string".'
        );

        $data = ['clientOptions' => array_replace(
            $this->validClientOptionsSettings(),
            [
            'trace' => 'falsy',
            ]
        )];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItValidatesClientOptionsEncodingIsAString()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "clientOptions[encoding]" with value array is expected to be of type "string", ' .
            'but is of type "array".'
        );

        $data = ['clientOptions' => array_replace(
            $this->validClientOptionsSettings(),
            [
            'encoding' => ['utf-8'],
            ]
        )];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItValidatesClientOptionsConnectionTimeoutIsAnInteger()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "clientOptions[connection_timeout]" with value "11" is expected to be of type "int", ' .
            'but is of type "string".'
        );

        $data = ['clientOptions' => array_replace(
            $this->validClientOptionsSettings(),
            [
            'connection_timeout' => '11',
            ]
        )];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItCanReceiveClient()
    {
        $data = array_merge([], [
            'client' => new Client([
                'wsdl' => 'test.wsdl',
            ]),
        ]);

        $resolver = SettingsResolver::create($data);

        $this->assertSame($data['client'], $resolver->resolve($data)['client']);
    }

    public function testItDefinesADefaultClient()
    {
        $resolver = SettingsResolver::create([]);

        $this->assertInstanceOf(Client::class, $resolver->resolve([])['client']);
    }

    public function testItDefinesADefaultClientSimulatorWhenInSimulatorMode()
    {
        $data = ['simulatorMode' => true];

        $resolver = SettingsResolver::create($data);

        $this->assertInstanceOf(Client::class, $resolver->resolve($data)['client']);
    }

    public function testItValidatesClientImplementsClientInterface()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "client" with value "new client" is expected to be of type ' .
            '"Placetopay\SoapClient\Contracts\ClientContract", but is of type "string".'
        );

        $data = ['client' => 'new client'];

        $resolver = SettingsResolver::create($data);
        $resolver->resolve($data);
    }

    public function testItsClientMustImplementClientContract()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "client" with value "stringClient" is expected to be of type ' .
            '"Placetopay\SoapClient\Contracts\ClientContract", but is of type "string".'
        );

        $data = array_merge([], ['client' => 'stringClient']);

        SettingsResolver::create($data)->resolve($data);
    }

    protected function validLoggerSettings(): array
    {
        return [
            'name' => 'valid name',
            'via' => new TestLogger(),
            'path' => 'valid/path',
        ];
    }

    protected function validClientOptionsSettings(): array
    {
        return [
            'soapVersion' => 2,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => true,
            'encoding' => 'UTF-8',
            'connection_timeout' => 11,
        ];
    }
}
