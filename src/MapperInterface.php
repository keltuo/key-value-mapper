<?php
declare(strict_types=1);

namespace KeyValueMapper;

/**
 * Interface MapperInterface
 *
 * @package KeyValueMapper
 */
interface MapperInterface
{
    public const TYPE_ARRAY = 'array';
    public const TYPE_OBJECT = 'object';
    public const TYPE_STRING = 'string';
    public const TYPE_NUMBER = 'number';
    public const TYPE_TIMESTAMP = 'timestamp';
    public const TYPE_DATETIME = 'datetime';
    public const TYPE_DATE = 'date';
    public const TYPE_CUSTOM_DATETIME = 'custom-datetime';
    public const TYPE_DATETIME_OBJECT = 'datetime-object';

    public function getMap(bool $mapBySourceKey = true): array;

    public function getList(string $mapName, bool $mapBySourceKey = true): array;

    public function setData(array $data): self;

    public function getMapDataSource(): array;
}
