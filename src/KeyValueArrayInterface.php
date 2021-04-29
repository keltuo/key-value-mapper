<?php
declare(strict_types=1);

namespace KeyValueMapper;

/**
 * Interface MapperInterface
 * @package KeyValueMapper
 */
interface KeyValueArrayInterface
{
    public function isEnabled(): bool;
    public function setEnabled(bool $enabled);
    public function getKeyToArray(): string;
    public function getValueToArray(): string;
    public function getValueAsArray(): array;
}
