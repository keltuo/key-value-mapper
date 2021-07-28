<?php
declare(strict_types=1);

namespace KeyValueMapper;

/**
 * Interface MapperInterface
 * @package KeyValueMapper
 */
interface MapperInterface
{
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';
    const TYPE_STRING = 'string';
    const TYPE_NUMBER = 'number';
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_DATETIME = 'datetime';
    const TYPE_DATE = 'date';
    const TYPE_CUSTOM_DATETIME = 'custom-datetime';
    /**
     * @param bool $mapBySourceKey
     * @return array<string|int|array|bool>
     */
    public function getMap(bool $mapBySourceKey = true): array;

    /**
     * @param string $mapName
     * @param bool $mapBySourceKey
     * @return array<string|number|array|bool>
     */
    public function getList(string $mapName, bool $mapBySourceKey = true): array;

    /**
     * @param array $data
     */
    public function setData(array $data);

    /** @return array<string|number|array|bool> */
    public function getMapDataSource(): array;
}
