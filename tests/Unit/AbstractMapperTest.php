<?php


namespace KeyValueMapper\Tests\Unit;


use KeyValueMapper\AbstractMapper;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractMapperTest
 * @package KeyValueMapper\Tests\Unit
 */
class AbstractMapperTest extends TestCase
{
    /**
     * @var AbstractMapper
     */
    protected AbstractMapper $mapper;

    protected function setUp()
    {
        $this->mapper = $this->getMockForAbstractClass(
            '\KeyValueMapper\AbstractMapper',
            [
                ['sourceKey' => 'targetKey']
            ]
        );
        parent::setUp();
    }

    public function testGetMapFromSource()
    {
        $mapper = $this->getMockForAbstractClass(
            '\KeyValueMapper\AbstractMapper',
            [
                ['sourceKey' => 'test value']
            ]
        )->setMapDataSource(['sourceKey' => 'targetKey']);
        $this->assertEquals(
            ['targetKey' => 'test value'],
            $mapper->getMap()
        );
        $this->assertEquals(
            ['targetKey' => 'test value'],
            $mapper->getMapFromSource()
        );
    }

    public function testGetMapFromTarget()
    {
        $mapper = $this->getMockForAbstractClass(
            '\KeyValueMapper\AbstractMapper',
            [
                ['targetKey' => 'test value']
            ]
        )->setMapDataSource(['sourceKey' => 'targetKey']);
        $this->assertEquals(
            ['sourceKey' => 'test value'],
            $mapper->getMap(false)
        );
        $this->assertEquals(
            ['sourceKey' => 'test value'],
            $mapper->getMapFromTarget()
        );
    }

    public function testGetMapWithDefaultValue()
    {
        $mapper = $this->getMockForAbstractClass(
            '\KeyValueMapper\AbstractMapper',
            [
                ['targetKey' => 'test value']
            ]
        )->setMapDataSource([
            'sourceKey' => 'targetKey',
            'sourceKeyDefault' => 'targetKeyDefault'
        ]);
        $mapper->defaultValues = ['sourceKeyDefault' => 'default value'];

        $this->assertEquals(
            [
                'sourceKey' => 'test value',
                'sourceKeyDefault' => 'default value'
            ],
            $mapper->getMap(false)
        );
    }

    public function testGetValueByKey()
    {
        $mapperBuilder = $this->getMockBuilder(
            '\KeyValueMapper\AbstractMapper',
        )->getMockForAbstractClass();
        $mapperBuilder->setData([
            'sourceKey' => 'test value',
        ]);
        $mapper = $mapperBuilder->setMapDataSource([
            'sourceKey' => 'targetKey',
        ]);
        $this->assertEquals(
            'test value',
            $mapper->getValueByKey('targetKey', null, false)
        );
    }

    public function testGetKey()
    {
        $mapperBuilder = $this->getMockBuilder(
            '\KeyValueMapper\AbstractMapper',
        )->getMockForAbstractClass();
        $mapper = $mapperBuilder->setMapDataSource([
            'sourceKey' => 'targetKey',
        ]);
        $this->assertEquals('targetKey', $mapper->getTargetKey('sourceKey'));
        $this->assertEquals('sourceKey', $mapper->getSourceKey('sourceKey'));

        $this->assertEquals('sourceKey', $mapper->getTargetKey('sourceKey', false));
        $this->assertEquals('targetKey', $mapper->getSourceKey('sourceKey', false));

        $this->assertEquals(null, $mapper->getTargetKey('targetKey', false));
        $this->assertEquals(null, $mapper->getSourceKey('targetKey', false));
    }
}
