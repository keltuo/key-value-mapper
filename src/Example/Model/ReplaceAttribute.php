<?php
declare(strict_types=1);

namespace KeyValueMapper\Example\Model;

use KeyValueMapper\AbstractMapper;
use KeyValueMapper\KeyValueArrayInterface;

/**
 * Class ReplaceAttribute
 * @package KeyValueMapper\Example\Model
 */
class ReplaceAttribute extends AbstractMapper implements KeyValueArrayInterface
{
    /** @var bool  */
    protected bool $enabled = true;
    /** @var array|string[] */
    protected array $map = [
        'name' => 'name',
        'value' => 'value',
    ];

    /**
     * @param array $data
     * @return AbstractMapper
     */
    public function setData(array $data): AbstractMapper
    {
        $outputData = $data;
        if (!array_key_exists('name', $data)) {
            foreach ($data as $key => $value) {
                $map = $this->map;
                $map['name'] = $key;
                $map['value'] = $value;
                $outputData = $map;
            }
        }
        return parent::setData($outputData);
    }

    /**
     * @return string
     */
    public function getKeyToArray(): string
    {
        return $this->data['name'];
    }

    /**
     * @return string
     */
    public function getValueToArray(): string
    {
        return $this->data['value'];
    }

    /**
     * @return array
     */
    public function getValueAsArray(): array
    {
        if ($this->isEnabled()) {
            return [$this->data['name'] => $this->data['value']];
        }
        return ['name' => $this->data['name'], 'value' => $this->data['value']];
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled(bool $enabled): ReplaceAttribute
    {
        $this->enabled = $enabled;
        return $this;
    }
}

