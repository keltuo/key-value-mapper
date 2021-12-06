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
