<?php

namespace Placetopay\CamaraComercioBogotaSdk\Support;

class CompanyDataTransformer
{
    /**
     * Transform and filter company data to a clean structure with English keys.
     *
     * @param array|null $companyData
     * @return array|null
     */
    public function transform(?array $companyData): ?array
    {
        if (!$companyData || !is_array($companyData)) {
            return null;
        }

        $transformed = [];

        // Basic Information
        if (isset($companyData['nur'])) {
            $transformed['nur'] = $this->filterValue($companyData['nur']);
        }
        if (isset($companyData['businessName'])) {
            $transformed['businessName'] = $this->filterValue($companyData['businessName']);
        }

        // Identification
        if (isset($companyData['identification']) && is_array($companyData['identification'])) {
            $identification = [];
            if (isset($companyData['identification']['type'])) {
                $identification['type'] = $this->filterValue($companyData['identification']['type']);
            }
            if (isset($companyData['identification']['number'])) {
                $identification['number'] = $this->filterValue($companyData['identification']['number']);
            }
            if (!empty($identification)) {
                $transformed['identification'] = $identification;
            }
        }

        // Registry Information
        if (isset($companyData['registry']) && is_array($companyData['registry'])) {
            $registry = [];
            if (isset($companyData['registry']['matricula'])) {
                $registry['registrationNumber'] = $this->filterValue($companyData['registry']['matricula']);
            }
            if (isset($companyData['registry']['status'])) {
                $registry['status'] = $this->filterValue($companyData['registry']['status']);
            }
            if (isset($companyData['registry']['registrationDate'])) {
                $registry['registrationDate'] = $this->filterValue($companyData['registry']['registrationDate']);
            }
            if (isset($companyData['registry']['constitutionDate'])) {
                $registry['constitutionDate'] = $this->filterValue($companyData['registry']['constitutionDate']);
            }
            if (isset($companyData['registry']['lastRenewalDate'])) {
                $registry['lastRenewalDate'] = $this->filterValue($companyData['registry']['lastRenewalDate']);
            }
            if (isset($companyData['registry']['chamber']) && is_array($companyData['registry']['chamber'])) {
                $chamber = [];
                if (isset($companyData['registry']['chamber']['id'])) {
                    $chamber['id'] = $this->filterValue($companyData['registry']['chamber']['id']);
                }
                if (isset($companyData['registry']['chamber']['name'])) {
                    $chamber['name'] = $this->filterValue($companyData['registry']['chamber']['name']);
                }
                if (!empty($chamber)) {
                    $registry['chamber'] = $chamber;
                }
            }
            if (!empty($registry)) {
                $transformed['registry'] = $registry;
            }
        }

        // Legal Information
        if (isset($companyData['legal']) && is_array($companyData['legal'])) {
            $legal = [];
            if (isset($companyData['legal']['legalForm'])) {
                $legal['legalForm'] = $this->filterValue($companyData['legal']['legalForm']);
            }
            if (isset($companyData['legal']['category'])) {
                $legal['category'] = $this->filterValue($companyData['legal']['category']);
            }
            if (isset($companyData['legal']['taxRegime'])) {
                $legal['taxRegime'] = $this->filterValue($companyData['legal']['taxRegime']);
            }
            if (!empty($legal)) {
                $transformed['legal'] = $legal;
            }
        }

        // Economic Activity
        if (isset($companyData['economicActivity']) && is_array($companyData['economicActivity'])) {
            $economicActivity = [];
            if (isset($companyData['economicActivity']['ciiu']) && is_array($companyData['economicActivity']['ciiu'])) {
                $ciiu = [];
                if (isset($companyData['economicActivity']['ciiu']['section'])) {
                    $ciiu['section'] = $this->filterValue($companyData['economicActivity']['ciiu']['section']);
                }
                if (isset($companyData['economicActivity']['ciiu']['code'])) {
                    $ciiu['code'] = $this->filterValue($companyData['economicActivity']['ciiu']['code']);
                }
                if (isset($companyData['economicActivity']['ciiu']['description'])) {
                    $ciiu['description'] = $this->filterValue($companyData['economicActivity']['ciiu']['description']);
                }
                if (!empty($ciiu)) {
                    $economicActivity['ciiu'] = $ciiu;
                }
            }
            if (!empty($economicActivity)) {
                $transformed['economicActivity'] = $economicActivity;
            }
        }

        // Contact Information
        if (isset($companyData['contacts']) && is_array($companyData['contacts'])) {
            $contacts = [];
            if (isset($companyData['contacts']['address'])) {
                $contacts['address'] = $this->filterValue($companyData['contacts']['address']);
            }
            if (isset($companyData['contacts']['departmentCode'])) {
                $contacts['departmentCode'] = $this->filterValue($companyData['contacts']['departmentCode']);
            }
            if (isset($companyData['contacts']['cityCode'])) {
                $contacts['cityCode'] = $this->filterValue($companyData['contacts']['cityCode']);
            }
            if (isset($companyData['contacts']['phones']) && is_array($companyData['contacts']['phones'])) {
                $phones = array_filter(
                    array_map([$this, 'filterValue'], $companyData['contacts']['phones']),
                    fn ($phone) => $phone !== null && $phone !== ''
                );
                if (!empty($phones)) {
                    $contacts['phones'] = array_values($phones);
                }
            }
            if (isset($companyData['contacts']['email'])) {
                $email = $this->filterValue($companyData['contacts']['email']);
                if ($email) {
                    $contacts['email'] = $email;
                }
            }
            if (isset($companyData['contacts']['website'])) {
                $website = $this->filterValue($companyData['contacts']['website']);
                if ($website) {
                    $contacts['website'] = $website;
                }
            }
            if (!empty($contacts)) {
                $transformed['contacts'] = $contacts;
            }
        }

        // Financial Information
        if (isset($companyData['financials']) && is_array($companyData['financials'])) {
            $financials = [];

            // Assets
            if (isset($companyData['financials']['assets']) && is_array($companyData['financials']['assets'])) {
                $assets = [];
                if (isset($companyData['financials']['assets']['current'])) {
                    $assets['current'] = $this->filterNumericValue($companyData['financials']['assets']['current']);
                }
                if (isset($companyData['financials']['assets']['nonCurrent'])) {
                    $assets['nonCurrent'] = $this->filterNumericValue($companyData['financials']['assets']['nonCurrent']);
                }
                if (isset($companyData['financials']['assets']['fixed'])) {
                    $assets['fixed'] = $this->filterNumericValue($companyData['financials']['assets']['fixed']);
                }
                if (isset($companyData['financials']['assets']['total'])) {
                    $assets['total'] = $this->filterNumericValue($companyData['financials']['assets']['total']);
                }
                if (!empty($assets)) {
                    $financials['assets'] = $assets;
                }
            }

            // Liabilities
            if (isset($companyData['financials']['liabilities']) && is_array($companyData['financials']['liabilities'])) {
                $liabilities = [];
                if (isset($companyData['financials']['liabilities']['current'])) {
                    $liabilities['current'] = $this->filterNumericValue($companyData['financials']['liabilities']['current']);
                }
                if (isset($companyData['financials']['liabilities']['nonCurrent'])) {
                    $liabilities['nonCurrent'] = $this->filterNumericValue($companyData['financials']['liabilities']['nonCurrent']);
                }
                if (isset($companyData['financials']['liabilities']['total'])) {
                    $liabilities['total'] = $this->filterNumericValue($companyData['financials']['liabilities']['total']);
                }
                if (!empty($liabilities)) {
                    $financials['liabilities'] = $liabilities;
                }
            }

            // Equity
            if (isset($companyData['financials']['equity']) && is_array($companyData['financials']['equity'])) {
                $equity = [];
                if (isset($companyData['financials']['equity']['netWorth'])) {
                    $equity['netWorth'] = $this->filterNumericValue($companyData['financials']['equity']['netWorth']);
                }
                if (!empty($equity)) {
                    $financials['equity'] = $equity;
                }
            }

            // Income
            if (isset($companyData['financials']['income']) && is_array($companyData['financials']['income'])) {
                $income = [];
                if (isset($companyData['financials']['income']['operationalIncome'])) {
                    $income['operationalIncome'] = $this->filterNumericValue($companyData['financials']['income']['operationalIncome']);
                }
                if (isset($companyData['financials']['income']['netProfitLoss'])) {
                    $income['netProfitLoss'] = $this->filterNumericValue($companyData['financials']['income']['netProfitLoss']);
                }
                if (!empty($income)) {
                    $financials['income'] = $income;
                }
            }

            if (!empty($financials)) {
                $transformed['financials'] = $financials;
            }
        }

        // Extra Information
        if (isset($companyData['extra']) && is_array($companyData['extra'])) {
            $extra = [];
            if (isset($companyData['extra']['employeesCount'])) {
                $employeesCount = $this->filterNumericValue($companyData['extra']['employeesCount']);
                if ($employeesCount !== null) {
                    $extra['employeesCount'] = $employeesCount;
                }
            }
            if (isset($companyData['extra']['typePerson'])) {
                $extra['typePerson'] = $this->filterValue($companyData['extra']['typePerson']);
            }
            if (isset($companyData['extra']['lastRenewalYear'])) {
                $extra['lastRenewalYear'] = $this->filterNumericValue($companyData['extra']['lastRenewalYear']);
            }
            if (!empty($extra)) {
                $transformed['extra'] = $extra;
            }
        }

        // Certificates
        if (isset($companyData['certificates']) && is_array($companyData['certificates'])) {
            $certificates = [];
            foreach ($companyData['certificates'] as $certificate) {
                if (!is_array($certificate)) {
                    continue;
                }
                $cert = [];
                if (isset($certificate['id'])) {
                    $cert['id'] = $this->filterValue($certificate['id']);
                }
                if (isset($certificate['name'])) {
                    $cert['name'] = $this->filterValue($certificate['name']);
                }
                if (isset($certificate['description'])) {
                    $cert['description'] = $this->filterValue($certificate['description']);
                }
                if (!empty($cert)) {
                    $certificates[] = $cert;
                }
            }
            if (!empty($certificates)) {
                $transformed['certificates'] = $certificates;
            }
        }

        // Representatives
        if (isset($companyData['representatives']) && is_array($companyData['representatives'])) {
            $representatives = [];
            foreach ($companyData['representatives'] as $representative) {
                if (!is_array($representative)) {
                    continue;
                }
                $rep = [];
                if (isset($representative['id'])) {
                    $rep['id'] = $this->filterValue($representative['id']);
                }
                if (isset($representative['name'])) {
                    $rep['name'] = $this->filterValue($representative['name']);
                }
                if (isset($representative['role'])) {
                    $rep['role'] = $this->filterValue($representative['role']);
                }
                if (isset($representative['identificationType'])) {
                    $rep['identificationType'] = $this->filterValue($representative['identificationType']);
                }
                if (isset($representative['personType'])) {
                    $rep['personType'] = $this->filterValue($representative['personType']);
                }
                if (!empty($rep)) {
                    $representatives[] = $rep;
                }
            }
            if (!empty($representatives)) {
                $transformed['representatives'] = $representatives;
            }
        }

        return !empty($transformed) ? $transformed : null;
    }

    /**
     * Filter out empty, null, or "Sin dato" values.
     *
     * @param mixed $value
     * @return string|null
     */
    protected function filterValue($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '' || strtolower($trimmed) === 'sin dato') {
                return null;
            }
            return $trimmed;
        }

        return $value;
    }

    /**
     * Filter and convert numeric values.
     *
     * @param mixed $value
     * @return float|null
     */
    protected function filterNumericValue($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float)$value;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '' || strtolower($trimmed) === 'sin dato') {
                return null;
            }
            if (is_numeric($trimmed)) {
                return (float)$trimmed;
            }
        }

        return null;
    }
}
