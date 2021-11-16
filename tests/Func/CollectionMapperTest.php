<?php
declare(strict_types=1);

namespace KeyValueMapper\Tests\Func;


use KeyValueMapper\MapperFactory;
use KeyValueMapper\Tests\BaseTestCase;

/**
 * Class CollectionMapperTest
 * @package KeyValueMapper\Tests\Func
 */
class CollectionMapperTest extends BaseTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapperFromSource = MapperFactory::createMapperCollection(
            MapperFactory::CALCULATION_DATA,
            $this->sourceData
        );

        $this->mapperFromTarget = MapperFactory::createMapperCollection(
            MapperFactory::CALCULATION_DATA,
            $this->targetData
        );
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

    public function testGetMappedKeyBySourceKey()
    {
        $this->assertEquals(
            'ins',
            $this->mapperFromSource->getMappedKey('insurance_company',  true)
        );
    }

    public function testGetMappedKeyByTargetKey()
    {
        $this->assertEquals(
            'insurance_company',
            $this->mapperFromTarget->getMappedKey('ins',  false)
        );
    }
}
