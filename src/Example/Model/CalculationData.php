<?php
declare(strict_types=1);

namespace KeyValueMapper\Example\Model;


use KeyValueMapper\AbstractMapper;
use KeyValueMapper\MapperInterface;

/**
 * Class CalculationData
 * @package KeyValueMapper\Example\Model
 */
class CalculationData extends AbstractMapper
{
    /**
     * Field "ins_start_date" as Output key must be in Timestamp format
     * @var string
     */
    protected string $ins_start_date = MapperInterface::TYPE_TIMESTAMP;
    /**
     * Field "insurance_date_start" as Input key must be in Date format
     * @var string
     */
    protected string $insurance_date_start = MapperInterface::TYPE_DATE;

    /**
     * If not set input key, but we can default values in output
     * @var array|int[]
     */
    public array $defaultValues = [
        'payment_frequency' => 0,
        'payment' => 0,
    ];
    /**
     * Array Map  input key => output key
     * @var array|string[]
     */
    protected array $map = [
        'insurance_company' => 'ins',
        'calculation_type' => 'calc_type',
        'payment_frequency' => 'payment',
        'insurance_type' => 'insurance',
        'insurance_date_start' => 'ins_start_date',
        'replace' => 'substitution_data'
    ];
    /**
     * Field calculation_type has mapped Enum values
     * @var array|int[]
     */
    protected array  $mapCalculationType = [
        'type1' => 'type_calculation_1',
        'type2' => 'type_calculation_2',
        'type3' => 'type_calculation_3',
    ];
}
