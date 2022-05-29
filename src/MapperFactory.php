<?php
declare(strict_types=1);

namespace KeyValueMapper;

use KeyValueMapper\Example\Collection\CalculationData;

/**
 * Class MapperFactory
 *
 * @package KeyValueMapper
 */
class MapperFactory
{
    public const CALCULATION_DATA = 'calculation_data';

    public static function createMapper(string $mapper, array $data = [], array $properties = []): AbstractMapper
    {
        if (!\class_exists($mapper)) {
            throw new \RuntimeException(\sprintf('Given class "%s", does not exist.', $mapper));
        }

        $mapperClass = new $mapper($data);

        if (!$mapperClass instanceof AbstractMapper) {
            throw new \RuntimeException(\sprintf(
                'Given class "%s", must be instance of %s',
                $mapper,
                AbstractMapper::class
            ));
        }

        if (\count($properties) > \count($mapperClass->insertProperties())) {
            throw new \RuntimeException(\sprintf(
                'You specified more injected properties than is allowed in the insertProperties method of class %s.',
                $mapperClass::class
            ));
        }

        if (\count($properties)) {
            $reflection = new \ReflectionClass($mapperClass);

            foreach ($mapperClass->insertProperties() as $property) {
                $propertyIndex = (int)\array_search($property, $mapperClass->insertProperties());

                if (!\array_key_exists($propertyIndex, $properties)) {
                    continue;
                }

                $mapperProperty = $reflection->getProperty($property);
                $mapperProperty->setAccessible(true);
                $mapperProperty->setValue($mapperClass, $properties[$propertyIndex]);
            }
        }

        return $mapperClass;
    }

    public static function createMapperCollection(string $method, array $dataset): AbstractCollection
    {
        switch ($method) {
            case self::CALCULATION_DATA:
                return (new CalculationData())->setData($dataset);

            default:
                throw new \InvalidArgumentException('Mapper is not implemented!');
        }
    }
}
