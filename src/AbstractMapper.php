<?php
declare(strict_types=1);

namespace KeyValueMapper;

use function Symfony\Component\String\u;

/**
 * Class AbstractMapper
 * @package KeyValueMapper
 */
abstract class AbstractMapper implements MapperInterface
{
    /**
     * @var string
     */
    protected string $customFormatDatetime = 'Y-m-d H:i:s';
    /**
     * @var array<string|number|bool|array>
     */
    protected array $data = [];
    /**
     * @var array<string|int>
     */
    protected array $map = [];
    /**
     * @var array<string|number|bool>
     */
    public array $defaultValues = [];

    /**
     * Bonus constructor.
     * @param array<string|number|array|bool> $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param bool $mapBySourceKey
     * @return array<string|int|array|bool>
     */
    public function getMap(bool $mapBySourceKey = true): array
    {
        return $this->createMap($mapBySourceKey);
    }
    /**
     * Shortcut
     * @return array[]|bool[]|number[]|string[]
     */
    public function getMapFromSource(): array
    {
        return $this->getMap(true);
    }

    /**
     * Shortcut
     * @return array[]|bool[]|number[]|string[]
     */
    public function getMapFromTarget(): array
    {
        return $this->getMap(false);
    }

    /**
     * @param string $mapName
     * @param bool $mapBySourceKey
     * @return array<string|number|array|bool>
     */
    public function getList(string $mapName, bool $mapBySourceKey = true): array
    {
        $outputList = [];
        try {
            $ref = new \ReflectionClass($this);
            $mapProperty = (string)u('map ' . $mapName)->camel();
            if ($ref->hasProperty($mapProperty)) {
                $mapList = $this->{$mapProperty};
                if (is_array($mapList)) {
                    foreach ($this->data as $key => $value) {
                        $searchValue = (string)strtoupper((string)$key);
                        if ($mapBySourceKey && array_key_exists($searchValue, $mapList)) {
                            $outputList[$mapList[$searchValue]] = $value;
                        } else {
                            if (!$mapBySourceKey && in_array($key, $mapList)) {
                                $searchKey = array_search($key, $mapList);
                                $outputList[$searchKey] = $value;
                            }
                        }
                    }
                }
            }
        } catch (\ReflectionException $exception) {

        }

        return $outputList;

    }

    /**
     * @param string $key
     * @param string|null $default
     * @param bool $mapBySourceKey
     * @return array<array|bool|number|string|null>|bool|number|string|null
     */
    public function getValueByKey(string $key, string $default = null, bool $mapBySourceKey = true)
    {
        $value = $default;
        if (array_key_exists($key, $this->getMap(!$mapBySourceKey))) {
            $value = $this->getMap(!$mapBySourceKey)[$key];
        }

        return $value;
    }

    /**
     * @param string $key
     * @param bool $mapBySourceKey
     * @return string|null
     */
    public function getTargetKey(string $key, bool $mapBySourceKey = true): ?string
    {
        if (array_key_exists($key, $this->map)) {
            if (!$mapBySourceKey) {
                return $key;
            }
            return $this->map[$key];
        }
        return null;
    }

    /**
     * @param string $key
     * @param bool $mapBySourceKey
     * @return string|null
     */
    public function getSourceKey(string $key, bool $mapBySourceKey = true): ?string
    {
        if (array_key_exists($key, $this->map)) {
            if (!$mapBySourceKey) {
                return $this->map[$key];
            }
            return $key;
        }
        return null;
    }

    /**
     * @param array $data
     * @return AbstractMapper
     */
    public function setData(array $data): AbstractMapper
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getMapDataSource(): array
    {
        return $this->map;
    }

    /**
     * @param array $mapData
     * @return $this
     */
    public function setMapDataSource(array $mapData = array()): self
    {
        $this->map = $mapData;
        return $this;
    }
    /**
     * @param bool $mapBySourceKey
     * @return array<array|bool|int|string>
     */
    protected function createMap(bool $mapBySourceKey = true): array
    {
        $ref = new \ReflectionClass($this);
        $out = [];
        foreach ($this->map as $key => $value) {
            $searchKey = $this->getSourceKey($key, $mapBySourceKey);
            $mapKey = $this->getTargetKey($key, $mapBySourceKey);
            if (array_key_exists($searchKey, $this->data)) {
                $out[$mapKey] = $this->data[$searchKey];
                $mapProperty = (string)u('map ' . $key)->camel();
                if ($ref->hasProperty($mapProperty)) {
                    if (is_array($this->data[$searchKey])) {
                        foreach ($this->data[$searchKey] as $arraySearchKey => $arraySearchValue) {
                            $searchValue = (string)u((string)$arraySearchValue)->upper();
                            if ($mapBySourceKey && array_key_exists($searchValue, $this->{$mapProperty})) {
                                $out[$mapKey][$arraySearchKey] = $this->{$mapProperty}[$searchValue];
                            } else {
                                if (!$mapBySourceKey && in_array($searchValue, $this->{$mapProperty})) {
                                    $out[$mapKey][$arraySearchKey] = array_search(
                                        $searchValue,
                                        $this->{$mapProperty}
                                    );
                                }
                            }
                        }
                    } else {
                        $searchValue = $this->data[$searchKey];
                        if ($mapBySourceKey && array_key_exists($searchValue, $this->{$mapProperty})) {
                            $out[$mapKey] = $this->{$mapProperty}[$searchValue];
                        } else {
                            if (!$mapBySourceKey && in_array($searchValue, $this->{$mapProperty})) {
                                $out[$mapKey] = array_search($searchValue, $this->{$mapProperty});
                            }
                        }
                    }
                } else {
                    if ($ref->hasProperty((string)$mapKey)) {
                        $value = is_array($this->data[$searchKey]) ? '' : $this->data[$searchKey];
                        $out[$mapKey] = $this->escapeValue(
                            (string)$value,
                            (string)$this->{$mapKey}
                        );
                    }
                }
            } else {
                if (
                    !array_key_exists($searchKey, $this->data)
                    && array_key_exists($mapKey, $this->defaultValues)
                ) {
                    $out[$mapKey] = $this->defaultValues[$mapKey];
                    if ($ref->hasProperty($mapKey)) {
                        $out[$mapKey] = $this->escapeValue(
                            (string)$this->defaultValues[$mapKey],
                            (string)$this->{$mapKey}
                        );
                    }
                }
            }
        }

        return $out;
    }

    /**
     * @param string $value
     * @param string $type
     * @return false|number|string|null
     */
    protected function escapeValue(string $value, string $type = MapperInterface::TYPE_STRING)
    {
        switch ($type) {
            case MapperInterface::TYPE_TIMESTAMP:
                return (int)strtotime($value);
            case MapperInterface::TYPE_DATE:
            case MapperInterface::TYPE_DATETIME:
            case MapperInterface::TYPE_CUSTOM_DATETIME:
                if (empty($value)) return null;
                if (!is_numeric($value)) $value = strtotime($value);
                return (new \DateTime())
                    ->setTimestamp((int)$value)
                    ->format($this->getDatetimeFormat($type));
            case MapperInterface::TYPE_NUMBER:
                return intval($value);
            default:
                return sprintf("%s", $value);
        }
    }

    /**
     * @param $type
     * @return string
     */
    protected function getDatetimeFormat($type): string
    {
        switch ($type) {
            case MapperInterface::TYPE_CUSTOM_DATETIME:
                return $this->customFormatDatetime;
            case MapperInterface::TYPE_DATETIME:
                return DATE_RFC3339;
            default:
                return 'Y-m-d';
        }
    }
}
