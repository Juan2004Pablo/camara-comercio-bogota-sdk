<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use PlacetoPay\Atropos\Logger\TestLogger;
use Placetopay\CamaraComercioBogotaSdk\Simulators\ClientSimulator;
use Placetopay\CamaraComercioBogotaSdk\Support\SettingsResolver;
use PlacetoPay\Tangram\Entities\Cache;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class SettingsResolverTest extends TestCase
{
    private array $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [];
    }

    public function testItDefinesADefaultProviderName()
    {
        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';

        $resolver = SettingsResolver::create($this->data);

        $this->assertEquals('Camara Comercio Bogota', $resolver->resolve($this->data)['providerName']);
    }

    public function testItValidatesProviderNameIsAString()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "providerName" with value array is expected to be of type "string", but is of type "array".');

        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';
        $this->data['providerName'] = ['Array'];

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItValidatesUrlIsAString()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "url" with value array is expected to be of type "string", but is of type "array".');

        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = ['Array'];

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItRequiresUsername()
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "username" is missing.');

        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItValidatesUsernameIsAString()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "username" with value array is expected to be of type "string", but is of type "array".');

        $this->data['username'] = ['Array'];
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItRequiresPassword()
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "password" is missing.');

        $this->data['username'] = 'test_user';
        $this->data['url'] = 'https://test.example.com';

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItValidatesPasswordIsAString()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "password" with value array is expected to be of type "string", but is of type "array".');

        $this->data['username'] = 'test_user';
        $this->data['password'] = ['Array'];
        $this->data['url'] = 'https://test.example.com';

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItDefinesADefaultClient()
    {
        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';

        $resolver = SettingsResolver::create($this->data);

        $this->assertInstanceOf(Client::class, $resolver->resolve($this->data)['client']);
    }

    public function testItDefinesADefaultClientSimulatorWhenInSimulatorMode()
    {
        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';
        $this->data['simulatorMode'] = true;

        $resolver = SettingsResolver::create($this->data);

        $this->assertInstanceOf(ClientSimulator::class, $resolver->resolve($this->data)['client']);
    }

    public function testItValidatesClientImplementsClientInterface()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "client" with value "new client" is expected to be of type "GuzzleHttp\ClientInterface", but is of type "string".');

        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';
        $this->data['client'] = 'new client';

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItDefinesADefaultCache()
    {
        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';

        $resolver = SettingsResolver::create($this->data);

        $this->assertInstanceOf(Cache::class, $resolver->resolve($this->data)['cache']);
    }

    public function testItValidatesClientImplementsCacheInterface()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "cache" with value "new cache" is expected to be of type "Psr\SimpleCache\CacheInterface", but is of type "string".');

        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';
        $this->data['cache'] = 'new cache';

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItDefinesSimulatorModeByDefaultAsFalse()
    {
        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';

        $resolver = SettingsResolver::create($this->data);

        $this->assertTrue($resolver->resolve($this->data)['simulatorMode']);
    }

    public function testItValidatesSimulatorModeIsBoolean()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "simulatorMode" with value "falsy" is expected to be of type "bool", but is of type "string".');

        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';
        $this->data['simulatorMode'] = 'falsy';

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItValidatesLoggerIsAnArray()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The nested option "logger" with value "logger" is expected to be of type array, but is of type "string".');

        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';
        $this->data['logger'] = 'logger';

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItValidatesLoggerNameIsAString()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "logger[name]" with value array is expected to be of type "string", but is of type "array".');

        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';
        $this->data['logger'] = array_replace($this->validLoggerSettings(), [
            'name' => ['array name'],
        ]);

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItValidatesLoggerViaIsRequired()
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "logger[via]" is missing.');

        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';

        $loggerSetting = $this->validLoggerSettings();
        unset($loggerSetting['via']);

        $this->data['logger'] = $loggerSetting;

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItValidatesLoggerViaImplementsLoggerInterface()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "logger[via]" with value "string via" is expected to be of type "Psr\Log\LoggerInterface", but is of type "string".');

        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';
        $this->data['logger'] = array_replace($this->validLoggerSettings(), [
            'via' => 'string via',
        ]);

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItValidatesLoggerPathIsAString()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "logger[path]" with value array is expected to be of type "string" or "null", but is of type "array".');

        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';
        $this->data['logger'] = array_replace($this->validLoggerSettings(), [
            'path' => ['array path'],
        ]);

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    public function testItDefinesLoggerDebugByDefaultAsFalse()
    {
        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';
        $this->data['logger'] = $this->validLoggerSettings();

        $resolver = SettingsResolver::create($this->data);

        $this->assertFalse($resolver->resolve($this->data)['logger']['debug']);
    }

    public function testItValidatesLoggerDebugIsABoolean()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "logger[debug]" with value "falsy" is expected to be of type "bool", but is of type "string".');

        $this->data['username'] = 'test_user';
        $this->data['password'] = 'test_pass';
        $this->data['url'] = 'https://test.example.com';
        $this->data['logger'] = array_replace($this->validLoggerSettings(), [
            'debug' => 'falsy',
        ]);

        $resolver = SettingsResolver::create($this->data);
        $resolver->resolve($this->data);
    }

    protected function validLoggerSettings(): array
    {
        return [
            'name' => 'valid name',
            'via' => new TestLogger(),
            'path' => 'valid/path',
        ];
    }
}
