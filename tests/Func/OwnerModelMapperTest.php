<?php
declare(strict_types=1);

namespace KeyValueMapper\Tests\Func;

use KeyValueMapper\Example\Model\Owner;
use KeyValueMapper\Tests\BaseTestCase;

/**
 * Class OwnerModelMapperTest
 * @package KeyValueMapper\Tests\Func
 */
class OwnerModelMapperTest extends BaseTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->sourceData = [
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
        ];
        $this->defaultExpectedMapSource = [
            'owner_kind' => 1,
            'owner_first_name' => 'first_name value',
            'owner_last_name' => 'last_name value',
            'owner_dob' => strtotime('2021-01-21'),
            'owner_id' => 'personal_id value',
            'owner_vat' => 'vat value',
            'owner_mail' => 'email value',
            'owner_tel' => 'phone value',
            'owner_street' => 'street value',
            'owner_street_nr' => 'street_number value',
            'owner_zip' => 'zip value',
        ];
        $this->mapperFromSource = new Owner($this->sourceData);
        $this->targetData = [
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
        ];
        $this->defaultExpectedMapTarget = [
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
        ];
        $this->mapperFromTarget = new Owner($this->targetData);
    }

    public function testGetMapBySource()
    {
        $this->assertEquals(
            $this->defaultExpectedMapSource,
            $this->mapperFromSource->getMapFromSource()
        );
    }

    public function testGetMapByTarget()
    {
        $this->assertEquals(
            $this->defaultExpectedMapTarget,
            $this->mapperFromTarget->getMapFromTarget()
        );
    }

    public function testGetValueByTargetKey()
    {
        $this->assertEquals(
            'first_name value',
            $this->mapperFromSource->getValueByKey('owner_first_name', null, false)
        );
    }

    public function testGetValueBySourceKey()
    {
        $this->assertEquals(
            'owner_first_name value',
            $this->mapperFromTarget->getValueByKey('first_name', null, true)
        );
    }

    public function testEscapeValues()
    {
        $this->assertEquals(
            1611183600,
            $this->mapperFromSource->getValueByKey('owner_dob', null, false)
        );
        $this->assertEquals(
            'zip value',
            $this->mapperFromSource->getValueByKey('owner_zip', null, false)
        );
        $this->assertEquals(
            '2021-01-21',
            $this->mapperFromTarget->getValueByKey('date_of_birth', null, true)
        );
    }

    public function testGetList()
    {
        $this->mapperFromSource->setData([
            1 => 'person value',
            2 => 'company value',
            3 => 'self employed value',
        ]);
        $this->assertEquals([
            'PERSON' => 'person value',
            'COMPANY' => 'company value',
            'SELF_EMPLOYED' => 'self employed value'
        ],
            $this->mapperFromSource->getList('subject', false)
        );
    }
}
