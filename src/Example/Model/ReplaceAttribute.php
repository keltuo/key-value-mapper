<?php
declare(strict_types=1);

namespace KeyValueMapper\Example\Model;

use KeyValueMapper\AbstractMapper;
use KeyValueMapper\KeyValueArrayInterface;

/**
 * Class ReplaceAttribute
 *
 * @package KeyValueMapper\Example\Model
 */
class ReplaceAttribute extends AbstractMapper implements KeyValueArrayInterface
{
    protected bool $enabled = true;

    protected array $map = [
        'name' => 'name',
        'value' => 'value',
    ];

    public function setData(array $data): AbstractMapper
    {
        $outputData = $data;

        if (!\array_key_exists('name', $data)) {
            foreach ($data as $key => $value) {
                $map = $this->map;
                $map['name'] = $key;
                $map['value'] = $value;
                $outputData = $map;
            }
        }

        return parent::setData($outputData);
    }

    public function getKeyToArray(): string
    {
        return $this->data['name'];
    }

    public function getValueToArray(): string
    {
        return $this->data['value'];
    }

    public function getValueAsArray(): array
    {
        if ($this->isEnabled()) {
            return [$this->data['name'] => $this->data['value']];
        }

        return ['name' => $this->data['name'], 'value' => $this->data['value']];
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): ReplaceAttribute
    {
        $this->enabled = $enabled;
        return $this;
    }
}
