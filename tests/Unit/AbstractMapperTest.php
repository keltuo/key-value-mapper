<?php
declare(strict_types=1);

namespace KeyValueMapper\Tests\Unit;

use KeyValueMapper\AbstractMapper;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractMapperTest
 *
 * @package KeyValueMapper\Tests\Unit
 */
class AbstractMapperTest extends TestCase
{
    protected AbstractMapper $mapper;

    public function testGetMapFromSource(): void
    {
        $mapper = $this->getMockForAbstractClass(
            '\KeyValueMapper\AbstractMapper',
            [
                ['sourceKey' => 'test value'],
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

    public function testGetMapFromTarget(): void
    {
        $mapper = $this->getMockForAbstractClass(
            '\KeyValueMapper\AbstractMapper',
            [
                ['targetKey' => 'test value'],
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

    public function testGetMapWithDefaultValue(): void
    {
        $mapper = $this->getMockForAbstractClass(
            '\KeyValueMapper\AbstractMapper',
            [
                ['targetKey' => 'test value'],
            ]
        )->setMapDataSource([
            'sourceKey' => 'targetKey',
            'sourceKeyDefault' => 'targetKeyDefault',
        ]);
        $mapper->setDefaultValues(['sourceKeyDefault' => 'default value']);

        $this->assertEquals(
            [
                'sourceKey' => 'test value',
                'sourceKeyDefault' => 'default value',
            ],
            $mapper->getMap(false)
        );
    }

    public function testGetValueByKey(): void
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

    public function testGetKey(): void
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

    public function testGetMapWithConstantsValues(): void
    {
        $mapperBuilder = $this->getMockBuilder(
            '\KeyValueMapper\AbstractMapper',
        )->getMockForAbstractClass();
        $mapper = $mapperBuilder->setConstantValues([
            'FirstConstant' => 'This is first constant.',
        ]);

        $data = $mapper->getMap();

        $this->assertSame(true, \array_key_exists('FirstConstant', $data));
        $this->assertEquals('This is first constant.', $data['FirstConstant']);
    }

    public function testMapByMapperClass(): void
    {
        $mapperBuilder = $this->getMockBuilder(
            '\KeyValueMapper\AbstractMapper',
        )->getMockForAbstractClass();

        $mapperClass = new class() extends AbstractMapper {
            protected array $map = [
                'name' => 'mappedName',
                'age' => 'mappedAge',
            ];
        };

        $mapper = $mapperBuilder->setMapByMapperClasses([
            'person' => $mapperClass::class,
            'invalidMapper' => 1,
        ])
        ->setMapDataSource([
            'id' => 'mappedId',
        ]);
        $mapper->setData([
            'id' => 10,
            'name' => 'Agent',
            'age' => 7,
        ]);

        $data = $mapper->getMap();

        $this->assertSame(true, \array_key_exists('person', $data));
        $this->assertSame('Agent', $data['person']['mappedName']);
        $this->assertSame(7, $data['person']['mappedAge']);
        $this->assertSame(10, $data['mappedId']);
    }

    protected function setUp(): void
    {
        $this->mapper = $this->getMockForAbstractClass(
            '\KeyValueMapper\AbstractMapper',
            [
                ['sourceKey' => 'targetKey'],
            ]
        );

        parent::setUp();
    }
}
