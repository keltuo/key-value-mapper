<?php
declare(strict_types=1);

namespace KeyValueMapper;

use function Symfony\Component\String\u;

/**
 * Class AbstractMapper
 *
 * @package KeyValueMapper
 */
abstract class AbstractMapper implements MapperInterface
{
    protected array $defaultValues = [];
    protected string $customFormatDatetime = 'Y-m-d H:i:s';
    protected array $data = [];
    protected array $map = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getMap(bool $mapBySourceKey = true): array
    {
        return $this->createMap($mapBySourceKey);
    }

    public function getMapFromSource(): array
    {
        return $this->getMap(true);
    }

    public function getMapFromTarget(): array
    {
        return $this->getMap(false);
    }

    public function getList(string $mapName, bool $mapBySourceKey = true): array
    {
        $outputList = [];

        try {
            $ref = new \ReflectionClass($this);
            $mapProperty = (string)u('map ' . $mapName)->camel();

            if ($ref->hasProperty($mapProperty)) {
                $mapList = $this->{$mapProperty};

                if (\is_array($mapList)) {
                    foreach ($this->data as $key => $value) {
                        $searchValue = (string)\strtoupper((string)$key);

                        if ($mapBySourceKey && \array_key_exists($searchValue, $mapList)) {
                            $outputList[$mapList[$searchValue]] = $value;
                        } else {
                            if (!$mapBySourceKey && \in_array($key, $mapList)) {
                                $searchKey = \array_search($key, $mapList);
                                $outputList[$searchKey] = $value;
                            }
                        }
                    }
                }
            }
        } catch (\ReflectionException) {

        }

        return $outputList;

    }

    public function getValueByKey(
        string $key,
        ?string $default = null,
        bool $mapBySourceKey = true,
    ): array|bool|int|float|string|null
    {
        $value = $default;

        if (\array_key_exists($key, $this->getMap(!$mapBySourceKey))) {
            $value = $this->getMap(!$mapBySourceKey)[$key];
        }

        return $value;
    }

    public function getTargetKey(string $key, bool $mapBySourceKey = true): ?string
    {
        if (\array_key_exists($key, $this->map)) {
            if (!$mapBySourceKey) {
                return $key;
            }

            return $this->map[$key];
        }

        return null;
    }

    public function getSourceKey(string $key, bool $mapBySourceKey = true): ?string
    {
        if (\array_key_exists($key, $this->map)) {
            if (!$mapBySourceKey) {
                return $this->map[$key];
            }

            return $key;
        }

        return null;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getMapDataSource(): array
    {
        return $this->map;
    }

    public function setMapDataSource(array $mapData = array()): self
    {
        $this->map = $mapData;
        return $this;
    }

    public function setDefaultValues(array $defaultValues): self
    {
        $this->defaultValues = $defaultValues;
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function createMap(bool $mapBySourceKey = true): array
    {
        $ref = new \ReflectionClass($this);
        $out = [];

        foreach ($this->map as $key => $value) {
            $searchKey = $this->getSourceKey($key, $mapBySourceKey);
            $mapKey = $this->getTargetKey($key, $mapBySourceKey);

            if (!\is_null($searchKey) && \array_key_exists($searchKey, $this->data)) {
                $out[$mapKey] = $this->data[$searchKey];
                $mapProperty = (string)u('map ' . $key)->camel();

                if ($ref->hasProperty($mapProperty)) {
                    if (\is_array($this->data[$searchKey])) {
                        foreach ($this->data[$searchKey] as $arraySearchKey => $arraySearchValue) {
                            $searchValue = (string)u((string)$arraySearchValue)->upper();

                            if ($mapBySourceKey && \array_key_exists($searchValue, $this->{$mapProperty})) {
                                $out[$mapKey][$arraySearchKey] = $this->{$mapProperty}[$searchValue];
                            } else {
                                if (!$mapBySourceKey && \in_array($searchValue, $this->{$mapProperty})) {
                                    $out[$mapKey][$arraySearchKey] = \array_search(
                                        $searchValue,
                                        $this->{$mapProperty}
                                    );
                                }
                            }
                        }
                    } else {
                        $searchValue = $this->data[$searchKey];

                        if ($mapBySourceKey && \array_key_exists($searchValue, $this->{$mapProperty})) {
                            $out[$mapKey] = $this->{$mapProperty}[$searchValue];
                        } else {
                            if (!$mapBySourceKey && \in_array($searchValue, $this->{$mapProperty})) {
                                $out[$mapKey] = \array_search($searchValue, $this->{$mapProperty});
                            }
                        }
                    }
                } else {
                    if ($ref->hasProperty((string)$mapKey)) {
                        $value = \is_array($this->data[$searchKey]) ? '' : $this->data[$searchKey];
                        $out[$mapKey] = $this->escapeValue(
                            (string)$value,
                            (string)$this->{$mapKey}
                        );
                    }
                }
            } else {
                if (!\is_null($mapKey) && \array_key_exists($mapKey, $this->defaultValues)) {
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
     * @throws \Exception
     */
    protected function escapeValue(
        string $value,
        string $type = MapperInterface::TYPE_STRING,
    ): false|int|float|string|null|\DateTime
    {
        switch ($type) {
            case MapperInterface::TYPE_TIMESTAMP:
                return (int)\strtotime($value);

            case MapperInterface::TYPE_DATE:
            case MapperInterface::TYPE_DATETIME:
            case MapperInterface::TYPE_CUSTOM_DATETIME:
                if (empty($value)) return null;

                if (!\is_numeric($value)) $value = \strtotime($value);
                return (new \DateTime())
                    ->setTimestamp((int)$value)
                    ->format($this->getDatetimeFormat($type));

            case MapperInterface::TYPE_NUMBER:
                return \intval($value);

            case MapperInterface::TYPE_DATETIME_OBJECT:
                return new \DateTime($value);

            default:
                return \sprintf("%s", $value);
        }
    }

    protected function getDatetimeFormat(string $type): string
    {
        return match ($type) {
            MapperInterface::TYPE_CUSTOM_DATETIME => $this->customFormatDatetime,
            MapperInterface::TYPE_DATETIME => \DATE_RFC3339,
            default => 'Y-m-d',
        };
    }
}
