<?php
declare(strict_types=1);

namespace KeyValueMapper\Example\Collection;

use KeyValueMapper\AbstractCollection;
use KeyValueMapper\Example\Model\Owner;
use KeyValueMapper\Example\Model\CalculationData as MapperCalculationData;
use KeyValueMapper\Example\Model\ReplaceAttribute;
use KeyValueMapper\KeyValueArrayInterface;

/**
 * Class CalculationData
 * @package KeyValueMapper\Example\Collection
 */
class CalculationData extends AbstractCollection
{
    /**
     * @param bool $mapBySourceKey
     * @return array
     */
    public function getMap(bool $mapBySourceKey = true): array
    {
        $calculationData = new MapperCalculationData($this->data);
        $this->addCollection($calculationData);

        if (!$mapBySourceKey) {
            $this->addCollection(new Owner((array)$this->data));
        }

        if (isset($this->data['owner'])) {
            $this->addCollection(new Owner((array)$this->data['owner']));
        }

        $this->addArrayCollection(
            $calculationData,
            ReplaceAttribute::class,
            'replace',
            $mapBySourceKey,
            function(KeyValueArrayInterface $class) use ($mapBySourceKey) {
                $class->setEnabled($mapBySourceKey);
                return $class;
            }
        );

        return parent::getMap($mapBySourceKey);
    }

    /**
     * @return array
     */
    public function getMapDataSource(): array
    {
        $this->addCollection(new MapperCalculationData());
        $this->addCollection(new Owner(), 'owner', self::TYPE_OBJECT);
        return parent::getMapDataSource();
    }
}
