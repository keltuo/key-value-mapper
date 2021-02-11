<?php
declare(strict_types=1);

namespace KeyValueMapper;

use InvalidArgumentException;
use KeyValueMapper\Exception\MatchFoundException;

/**
 * Class AbstractCollection
 * @package KeyValueMapper
 */
abstract class AbstractCollection implements MapperInterface
{
    /** @var MapperInterface[] */
    protected array $collections = [];
    /** @var array<string|number|array|bool> */
    protected array $data = [];
    /** @var array<string|number|array|bool> */
    protected array $outputMap = [];
    /** @var array<string|number|array|bool> */
    protected array $outputList = [];
    /** @var array<string|number|array|bool>  */
    protected array $mapDataSource = [];

    /**
     * AbstractCollection constructor.
     * @param array $collections
     */
    public function __construct(array $collections = [])
    {
        $this->collections = $collections;
    }

    /**
     * @param string $key
     * @param string|null|array $default
     * @param bool $mapBySourceKey
     * @return array<array|bool|number|string|null>|bool|number|string|null
     */
    public function getValueByKey(string $key, $default = null, bool $mapBySourceKey = true)
    {
        $value = $default;
        $dataSource = $this->getMap(!$mapBySourceKey);
        $this->getKeyFromArray($dataSource, !$mapBySourceKey, $key, $value);
        return $value;
    }

    /**
     * @param  string  $key
     * @param  bool  $mapBySourceKey
     * @return string
     */
    public function getMappedKey(string $key, bool $mapBySourceKey = true)
    {
        $dataSource = $this->getMapDataSource();
        $returnKey = $key;
        $this->getKeyFromArray($dataSource, $mapBySourceKey, $key, $returnKey);
        return $returnKey;
    }

    /**
     * @return array
     */
    public function getMapDataSource(): array
    {
        if (empty($this->mapDataSource)) {
            $this->mapDataSource = [];
            foreach ($this->collections as $collectionKey => $collectionValue) {
                $outputMap = [];
                if (is_array($collectionValue)) {
                    $outputMap[$collectionKey] = $this->getDataSourceMapFromArrayCollection($collectionValue);
                } else {
                    $outputMapData = $collectionValue->getMapDataSource();
                    if (is_string($collectionKey)) {
                        $outputMap[$collectionKey] = $outputMapData;
                    } else {
                        $outputMap = $outputMapData;
                    }
                }
                $this->mapDataSource = array_merge($this->mapDataSource, $outputMap);
            }
        }
        return $this->mapDataSource;
    }

    /**
     * Shortcut
     * @return array<string|number|array|bool>
     */
    public function getMapFromSource(): array
    {
        return $this->getMap();
    }

    /**
     * Shortcut
     * @return array<string|number|array|bool>
     */
    public function getMapFromTarget(): array
    {
        return $this->getMap(false);
    }

    /**
     * @param bool $mapBySourceKey
     * @return array<string|number|array|bool>
     */
    public function getMap(bool $mapBySourceKey = true): array
    {
        if (empty($this->outputMap)) {
            $this->outputMap = [];
            foreach ($this->collections as $collectionKey => $collectionValue) {
                $outputMap = [];
                if (is_array($collectionValue)) {
                    $outputMap[$collectionKey] = $this->getMapFromArrayCollection($collectionValue, $mapBySourceKey);
                    $this->outputMap = array_merge($this->outputMap, $outputMap);
                } else {
                    $outputMapData = $collectionValue->getMap($mapBySourceKey);
                    if (is_string($collectionKey)) {
                        $outputMap[$collectionKey] = $outputMapData;
                    } else {
                        $outputMap = $outputMapData;
                    }
                    $this->outputMap = array_merge($this->outputMap, $outputMap);
                }
            }
        }

        return $this->outputMap;
    }

    /**
     * @param string $mapName
     * @param bool $mapBySourceKey
     * @return array<string|number|array|bool>
     */
    public function getList(string $mapName, bool $mapBySourceKey = true): array
    {
        if (empty($this->outputList)) {
            $this->outputList = [];
            foreach ($this->collections as $collection) {
                $this->outputList = array_merge($this->outputList, $collection->getList($mapName, $mapBySourceKey));
            }
        }

        return $this->outputList;
    }

    /**
     * @return MapperInterface[]
     */
    public function getCollections(): array
    {
        return $this->collections;
    }

    /**
     * @param  MapperInterface  $collection
     * @param  null|string|number  $key
     * @param  string  $type
     * @return $this
     */
    public function addCollection(
        MapperInterface $collection,
        $key = null,
        $type = MapperInterface::TYPE_ARRAY
    ): AbstractCollection
    {
        if (is_null($key)) {
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

    /**
     * @param  AbstractMapper  $mapper
     * @param $collectionClassName
     * @param  string  $mappedKey
     * @param  bool  $byMapKey
     * @return AbstractCollection
     */
    public function addArrayCollection(
        AbstractMapper $mapper,
        $collectionClassName,
        string $mappedKey,
        bool $byMapKey = true
    ): AbstractCollection
    {
        $sourceKey = $mapper->getSourceKey($mappedKey, $byMapKey);
        $targetKey = $mapper->getTargetKey($mappedKey, $byMapKey);
        if (isset($this->data[$sourceKey])) {
            foreach ($this->data[$sourceKey] as $itemsListData) {
                $class = $this->createCollectionFromClassName($collectionClassName);
                $this->addCollection($class->setData((array)$itemsListData), $targetKey);
            }
            unset($this->data[$sourceKey]);
        }
        return $this;
    }

    /**
     * @return array<string|number|array|bool>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<string|number|array|bool> $data
     * @return AbstractCollection
     */
    public function setData(array $data): AbstractCollection
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param $className
     * @return MapperInterface
     */
    protected function createCollectionFromClassName($className): MapperInterface
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException('Class [' . $className . '] not exists');
        }
        $class = new $className();
        if (!is_a($class, MapperInterface::class)) {
            throw new InvalidArgumentException('Class [' . $className . '] has not implemented MapperInterface');
        }
        return $class;
    }

    /**
     * @param array $collection
     * @param bool $byMapKey
     * @return array
     */
    protected function getMapFromArrayCollection(array $collection = [], bool $byMapKey = true): array
    {
        $output = [];
        foreach ($collection as $keySubCollection => $subCollection) {
            if (is_array($subCollection)) {
                $output[$keySubCollection][] = $this->getMapFromArrayCollection($subCollection, $byMapKey);
            } else {
                $output[$keySubCollection] = $subCollection->getMap($byMapKey);
            }
        }
        return $output;
    }

    /**
     * @param array $collection
     * @return array
     */
    protected function getDataSourceMapFromArrayCollection(array $collection = []): array
    {
        $output = [];
        foreach ($collection as $keySubCollection => $subCollection) {
            if (is_array($subCollection)) {
                $output[$keySubCollection][] = $this->getDataSourceMapFromArrayCollection($subCollection);
            } else {
                $output[$keySubCollection] = $subCollection->getMapDataSource();
            }
        }
        return $output;
    }

    /**
     * @param array $dataSource
     * @param bool $mapBySourceKey
     * @param mixed $key
     * @param mixed $returnKey
     */
    protected function getKeyFromArray(array &$dataSource, bool $mapBySourceKey, $key,  &$returnKey): void
    {
        if(array_key_exists($key, $dataSource)) {
            $returnKey = $dataSource[$key];
        } else {
            try {
                array_walk_recursive(
                    $dataSource,
                    function ($dataSourceItem, $dataSourceKey) use ($mapBySourceKey, $key, &$returnKey) {
                        if ($mapBySourceKey && $dataSourceKey == $key) {
                            $returnKey = $dataSourceItem;
                            throw new MatchFoundException('I found him');
                        } else if (
                            !$mapBySourceKey
                            && $dataSourceItem == $key
                        ) {
                            $returnKey = $dataSourceKey;
                            throw new MatchFoundException('I found him');
                        }
                    },
                    $returnKey
                );
            } catch (MatchFoundException $exception) {
                $a = $returnKey;
            }
        }
    }

}
