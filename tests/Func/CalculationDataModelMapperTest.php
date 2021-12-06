<?php
declare(strict_types=1);

namespace KeyValueMapper\Tests\Func;


use KeyValueMapper\Example\Model\CalculationData;
use KeyValueMapper\Tests\BaseTestCase;

/**
 * Class CalculationDataModelMapperTest
 * @package KeyValueMapper\Tests\Func
 */
class CalculationDataModelMapperTest extends BaseTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->sourceData = [
            'insurance_company' => 'insurance_company value',
            'calculation_type' => 'calculation_type value',
            'payment_frequency' => 'payment_frequency value',
            'insurance_type' => 'type1',
            'insurance_date_start' => '2021-01-21'
        ];
        $this->defaultExpectedMapSource = [
            'ins' => 'insurance_company value',
            'calc_type' => 'calculation_type value',
            'payment' => 'payment_frequency value',
            'insurance' => 'type1',
            'ins_start_date' => strtotime('2021-01-21'),
        ];
        $this->mapperFromSource = new CalculationData($this->sourceData);
        $this->targetData = [
            'ins' => 'ins value',
            'calc_type' => 'calc_type value',
            'payment' => 'payment value',
            'insurance' => 'type_calculation_1',
            'ins_start_date' => strtotime('2021-01-21'),
        ];
        $this->defaultExpectedMapTarget = [
            'insurance_company' => 'ins value',
            'calculation_type' => 'calc_type value',
            'payment_frequency' => 'payment value',
            'insurance_type' => 'type_calculation_1',
            'insurance_date_start' => '2021-01-21',
        ];
        $this->mapperFromTarget = new CalculationData($this->targetData);
    }

    public function testGetMapBySource()
    {
        $this->assertEquals(
            $this->defaultExpectedMapSource,
            (array)$this->mapperFromSource->getMapFromSource()
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
            'insurance_company value',
            $this->mapperFromSource->getValueByKey('ins', null, false)
        );
    }

    public function testGetValueBySourceKey()
    {
        $this->assertEquals(
            'ins value',
            $this->mapperFromTarget->getValueByKey('insurance_company', null, true)
        );
    }

    public function testEscapeValues()
    {
        $this->assertEquals(
            1611183600,
            $this->mapperFromSource->getValueByKey('ins_start_date', null, false)
        );
    }
}
