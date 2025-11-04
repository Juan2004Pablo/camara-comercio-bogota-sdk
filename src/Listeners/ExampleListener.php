<?php

namespace Placetopay\CamaraComercioBogotaSdk\Listeners;

use Placetopay\CamaraComercioBogotaSdk\Events\ExampleEvent;

class ExampleListener
{
    public function execute(ExampleEvent $event): void
    {
        //
    }

    public function getEventsMethodsToDispatcher(): array
    {
        return [
            ExampleEvent::class => [
                [$this, 'execute'],
            ],
        ];
    }
}
