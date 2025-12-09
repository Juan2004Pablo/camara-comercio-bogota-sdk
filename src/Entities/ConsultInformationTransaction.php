<?php

namespace Placetopay\CamaraComercioBogotaSdk\Entities;

use PlacetoPay\Base\Entities\Person;
use PlacetoPay\Base\Exceptions\BaseException;
use PlacetoPay\Base\Messages\AdministrativeTransaction;
use PlacetoPay\Base\Traits\LoaderTrait;

class ConsultInformationTransaction extends AdministrativeTransaction
{
    use LoaderTrait;

    protected Person $person;
    protected ?array $company = null;

    /**
     * @throws BaseException
     */
    public function __construct(array $data = [])
    {
        $this->load($data, [
            'person',
        ]);

        parent::__construct($data);
    }

    public function person(): Person
    {
        return $this->person;
    }

    public function setCompany(?array $company): void
    {
        $this->company = $company;
    }

    public function company(): ?array
    {
        return $this->company;
    }
}
