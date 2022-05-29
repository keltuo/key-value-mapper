<?php
declare(strict_types=1);

namespace KeyValueMapper\Tests\Unit;

use KeyValueMapper\AbstractMapper;
use KeyValueMapper\MapperFactory;
use PHPUnit\Framework\TestCase;

class MapperFactoryTest extends TestCase
{
    public function testCreateMapperClassNotExist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Given class "testClass", does not exist.');
        MapperFactory::createMapper('testClass');
    }

    public function testCreateMapperMustBeInstanceOfAbstractMapper(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Given class "KeyValueMapper\MapperFactory", must be instance of KeyValueMapper\AbstractMapper'
        );
        MapperFactory::createMapper(MapperFactory::class);
    }

    public function testCreateMapperSpecifyMoreInjectedPropertiesThanIsAllowed(): void
    {
        $this->expectException(\RuntimeException::class);
        MapperFactory::createMapper($this->getMapperMock()::class, [], ['first', 'second']);
    }

    public function testCreateMapper(): void
    {
        $mapper = MapperFactory::createMapper($this->getMapperMock()::class, [], ['unitTest']);
        $this->assertSame('unitTest', $mapper->getTestProperty());
    }

    private function getMapperMock(): AbstractMapper
    {
        return new class() extends AbstractMapper {
            private string $testProperty;
            private string $secondTestProperty;

            public function getTestProperty(): string
            {
                return $this->testProperty;
            }

            public function insertProperties(): array
            {
                return ['testProperty'];
            }
        };
    }
}
