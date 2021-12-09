<?php
declare(strict_types=1);

namespace KeyValueMapper;

use InvalidArgumentException;
use KeyValueMapper\Exception\MatchFoundException;

/**
 * Class AbstractCollection
 *
 * @package KeyValueMapper
 */
abstract class AbstractCollection implements MapperInterface
{
    protected array $collections = [];

    protected array $data = [];

    protected array $outputMap = [];

    protected array $outputList = [];

    protected array $mapDataSource = [];

    public function __construct(array $collections = [])
    {
        $this->collections = $collections;
    }

    public function getValueByKey(
        string $key,
        array|bool|int|float|string|null $default = null,
        bool $mapBySourceKey = true,
    ): array|bool|int|float|string|null {
        $value = $default;
        $dataSource = $this->getMap(!$mapBySourceKey);
        $this->getKeyFromArray($dataSource, !$mapBySourceKey, $key, $value);
        return $value;
    }

    public function getMappedKey(string $key, bool $mapBySourceKey = true): array|bool|int|float|string|null
    {
        $dataSource = $this->getMapDataSource();
        $returnKey = $key;
        $this->getKeyFromArray($dataSource, $mapBySourceKey, $key, $returnKey);
        return $returnKey;
    }

    public function getMapDataSource(): array
    {
        if (empty($this->mapDataSource)) {
            $this->mapDataSource = [];

            foreach ($this->collections as $collectionKey => $collectionValue) {
                $outputMap = [];

                if (\is_array($collectionValue)) {
                    $outputMap[$collectionKey] = $this->getDataSourceMapFromArrayCollection($collectionValue);
                } else {
                    $outputMapData = $collectionValue->getMapDataSource();

                    if (\is_string($collectionKey)) {
                        $outputMap[$collectionKey] = $outputMapData;
                    } else {
                        $outputMap = $outputMapData;
                    }
                }

                $this->mapDataSource = \array_merge($this->mapDataSource, $outputMap);
            }
        }

        return $this->mapDataSource;
    }

    public function getMapFromSource(): array
    {
        return $this->getMap();
    }

    public function getMapFromTarget(): array
    {
        return $this->getMap(false);
    }

    public function getMap(bool $mapBySourceKey = true): array
    {
        if (empty($this->outputMap)) {
            $this->outputMap = [];

            foreach ($this->collections as $collectionKey => $collectionValue) {
                $outputMap = [];

                if (\is_array($collectionValue)) {
                    $outputMap[$collectionKey] = $this->getMapFromArrayCollection($collectionValue, $mapBySourceKey);
                } else {
                    $outputMapData = $collectionValue->getMap($mapBySourceKey);

                    if (\is_string($collectionKey)) {
                        $outputMap[$collectionKey] = $outputMapData;
                    } else {
                        $outputMap = $outputMapData;
                    }
                }

                $this->outputMap = \array_merge($this->outputMap, $outputMap);
            }
        }

        return $this->outputMap;
    }

    public function getList(string $mapName, bool $mapBySourceKey = true): array
    {
        if (empty($this->outputList)) {
            $this->outputList = [];

            foreach ($this->collections as $collection) {
                $this->outputList = \array_merge($this->outputList, $collection->getList($mapName, $mapBySourceKey));
            }
        }

        return $this->outputList;
    }

    /**
     * @return array<MapperInterface>
     */
    public function getCollections(): array
    {
        return $this->collections;
    }

    public function addCollection(
        MapperInterface $collection,
        ?string $key = null,
        string $type = MapperInterface::TYPE_ARRAY,
    ): AbstractCollection {
        if (\is_null($key)) {
            $this->collections[] = $collection;
        } else {
            switch ($type) {
                case MapperInterface::TYPE_ARRAY:
                    $this->collections[$key][] = $collection;
                    break;

                case MapperInterface::TYPE_OBJECT:
                    $this->collections[$key] = $collection;
                    break;
            }
        }

        return $this;
    }

    public function addArrayCollection(
        AbstractMapper $mapper,
        string $collectionClassName,
        string $mappedKey,
        bool $byMapKey = true,
        ?\Closure $classCallback = null,
    ): AbstractCollection {
        $sourceKey = $mapper->getSourceKey($mappedKey, $byMapKey);
        $targetKey = $mapper->getTargetKey($mappedKey, $byMapKey);

        if (isset($this->data[$sourceKey])) {
            foreach ($this->data[$sourceKey] as $key => $itemsListData) {
                $class = $this->createCollectionFromClassName($collectionClassName);

                if (!\is_null($classCallback)) {
                    $class = $classCallback($class);
                }

                $itemsListDataArray = \is_array($itemsListData) ? $itemsListData : [];

                if (!\is_numeric($key)) {
                    $itemsListDataArray[$key] = $itemsListData;
                }

                $this->addCollection($class->setData((array)$itemsListDataArray), $targetKey);
            }

            unset($this->data[$sourceKey]);
        }

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): AbstractCollection
    {
        $this->data = $data;

        return $this;
    }

    protected function createCollectionFromClassName(string $className): MapperInterface
    {
        if (!\class_exists($className)) {
            throw new InvalidArgumentException('Class [' . $className . '] not exists');
        }

        $class = new $className();

        if (!\is_a($class, MapperInterface::class)) {
            throw new InvalidArgumentException('Class [' . $className . '] has not implemented MapperInterface');
        }

        return $class;
    }

    protected function getMapFromArrayCollection(array $collection = [], bool $byMapKey = true): array
    {
        $output = [];

        foreach ($collection as $keySubCollection => $subCollection) {
            if (\is_array($subCollection)) {
                $output[$keySubCollection][] = $this->getMapFromArrayCollection($subCollection, $byMapKey);
            } else {
                if ($subCollection instanceof KeyValueArrayInterface && $subCollection->isEnabled()) {
                    $output[$subCollection->getKeyToArray()] = $subCollection->getValueToArray();
                } else {
                    $output[$keySubCollection] = $subCollection->getMap($byMapKey);
                }
            }
        }

        return $output;
    }

    protected function getDataSourceMapFromArrayCollection(array $collection = []): array
    {
        $output = [];

        foreach ($collection as $keySubCollection => $subCollection) {
            if (\is_array($subCollection)) {
                $output[$keySubCollection][] = $this->getDataSourceMapFromArrayCollection($subCollection);
            } else {
                $output[$keySubCollection] = $subCollection->getMapDataSource();
            }
        }

        return $output;
    }

    protected function getKeyFromArray(
        array &$dataSource,
        bool $mapBySourceKey,
        string $key,
        array|bool|int|float|string|null &$returnKey,
    ): void {
        if (\array_key_exists($key, $dataSource)) {
            $returnKey = $dataSource[$key];
        } else {
            try {
                \array_walk_recursive(
                    $dataSource,
                    static function ($dataSourceItem, $dataSourceKey) use ($mapBySourceKey, $key, &$returnKey): void {
                        if ($mapBySourceKey
                            && $dataSourceKey === $key
                        ) {
                            $returnKey = $dataSourceItem;
                            throw new MatchFoundException('I found him');
                        }

                        if (!$mapBySourceKey
                            && $dataSourceItem === $key
                        ) {
                            $returnKey = $dataSourceKey;
                            throw new MatchFoundException('I found him');
                        }
                    },
                    $returnKey
                );
            } catch (MatchFoundException) {
            }
        }
    }
}
