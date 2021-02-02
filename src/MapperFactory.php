<?php
declare(strict_types=1);

namespace KeyValueMapper;

use KeyValueMapper\Example\Collection\CalculationData;

/**
 * Class MapperFactory
 * @package KeyValueMapper
 */
class MapperFactory
{
    const CALCULATION_DATA = 'calculation_data';
    /**
     * @param  string  $method
     * @param  array<string|number|array|bool>  $dataset
     * @return AbstractCollection
     */
    public static function createMapperCollection(string $method, array $dataset): AbstractCollection
    {
        switch ($method) {
            case self::CALCULATION_DATA:
                return new CalculationData($dataset);
            default:
                throw new \InvalidArgumentException('Mapper is not implemented!');
        }
    }


}
