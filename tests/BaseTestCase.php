<?php
declare(strict_types=1);

namespace KeyValueMapper\Tests;

use KeyValueMapper\MapperInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class BaseTestCase
 * @package KeyValueMapper\Tests
 */
class BaseTestCase extends TestCase
{
    /** @var MapperInterface */
    protected MapperInterface $mapperFromSource;
    /** @var MapperInterface  */
    protected MapperInterface $mapperFromTarget;
    /** @var array  */
    protected array $sourceData = [];
    /** @var array  */
    protected array $targetData = [];
    /** @var array  */
    protected array $defaultExpectedMapSource = [];
    /** @var array  */
    protected array $defaultExpectedMapTarget = [];

    protected function setUp(): void
    {
        parent::setUp();
        /** Default Source keys */
        $this->sourceData = [
            'insurance_company' => 'insurance_company value',
            'calculation_type' => 'calculation_type value',
            'payment_frequency' => 'payment_frequency value',
            'insurance_type' => 'type1',
            'insurance_date_start' => '2021-01-21',
            'owner' => [
                'subject' => 'PERSON',
                'first_name' => 'first_name value',
                'last_name' => 'last_name value',
                'date_of_birth' => '2021-01-21',
                'personal_id' => 'personal_id value',
                'vat' => 'vat value',
                'email' => 'email value',
                'phone' => 'phone value',
                'street' => 'street value',
                'street_number' => 'street_number value',
                'zip' => 'zip value',
            ],
            'replace' => [
                ['name' => 'key_name', 'value' => 'key value'],
                ['name' => 'key_name1', 'value' => 'key value 1'],
            ]
        ];
        $this->defaultExpectedMapSource = [
            'ins' => 'insurance_company value',
            'calc_type' => 'calculation_type value',
            'payment' => 'payment_frequency value',
            'insurance' => 'type1',
            'ins_start_date' => strtotime('2021-01-21'),
            'owner_kind' => 1,
            'owner_first_name' => 'first_name value',
            'owner_last_name' => 'last_name value',
            'owner_dob' => strtotime('2021-02-10'),
            'owner_id' => 'personal_id value',
            'owner_vat' => 'vat value',
            'owner_mail' => 'email value',
            'owner_tel' => 'phone value',
            'owner_street' => 'street value street_number value zip value',
            'owner_street_nr' => 'street_number value',
            'owner_zip' => 'zip value',
            'substitution_data' => [
                'key_name' => 'key value',
                'key_name1' => 'key value 1',
            ]
        ];

        /** Default Target keys */
        $this->targetData = [
            'ins' => 'ins value',
            'calc_type' => 'calc_type value',
            'payment' => 'payment value',
            'insurance' => 'type_calculation_1',
            'ins_start_date' => strtotime('2021-01-21'),
            'owner_kind' => 1,
            'owner_first_name' => 'owner_first_name value',
            'owner_last_name' => 'owner_last_name value',
            'owner_dob' => strtotime('2021-01-21'),
            'owner_id' => 'owner_id value',
            'owner_vat' => 'owner_vat value',
            'owner_mail' => 'owner_mail value',
            'owner_tel' => 'owner_tel value',
            'owner_street' => 'owner_street value',
            'owner_street_nr' => 'owner_street_nr value',
            'owner_zip' => 'owner_zip value',
            'substitution_data' => [
                'key_name' => 'key value',
                'key_name1' => 'key value 1',
            ],
        ];
        $this->defaultExpectedMapTarget = [
            'insurance_company' => 'ins value',
            'calculation_type' => 'calc_type value',
            'payment_frequency' => 'payment value',
            'insurance_type' => 'type_calculation_1',
            'insurance_date_start' => '2021-01-21',
            'subject' => 'PERSON',
            'first_name' => 'owner_first_name value',
            'last_name' => 'owner_last_name value',
            'date_of_birth' => '2021-01-21',
            'personal_id' => 'owner_id value',
            'vat' => 'owner_vat value',
            'email' => 'owner_mail value',
            'phone' => 'owner_tel value',
            'street' => 'owner_street value',
            'street_number' => 'owner_street_nr value',
            'zip' => 'owner_zip value',
            'replace' => [
                ['name' => 'key_name', 'value' => 'key value'],
                ['name' => 'key_name1', 'value' => 'key value 1'],
            ],
        ];
    }
}
