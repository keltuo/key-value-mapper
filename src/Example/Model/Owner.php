<?php
declare(strict_types=1);

namespace KeyValueMapper\Example\Model;

use KeyValueMapper\AbstractMapper;
use KeyValueMapper\MapperInterface;

/**
 * Class Owner
 *
 * @package KeyValueMapper\Example\Model
 */
class Owner extends AbstractMapper
{
    /**
     * Field "owner_dob" as Output key must be in Timestamp format
     */
    protected string $owner_dob = MapperInterface::TYPE_TIMESTAMP;

    /**
     * Field "date_of_birth" as Input key must be in Date format
     */
    protected string $date_of_birth = MapperInterface::TYPE_DATE;

    /**
     * Field "owner_zip" as Output key must be in String format
     */
    protected string $owner_zip = MapperInterface::TYPE_STRING;

    /**
     * Array Map input key => output key
     *
     * @var array<string>
     */
    protected array $map = [
        'subject' => 'owner_kind',
        'first_name' => 'owner_first_name',
        'last_name' => 'owner_last_name',
        'date_of_birth' => 'owner_dob',
        'personal_id' => 'owner_id',
        'vat' => 'owner_vat',
        'email' => 'owner_mail',
        'phone' => 'owner_tel',
        'street' => 'owner_street',
        'street_number' => 'owner_street_nr',
        'zip' => 'owner_zip',
    ];

    /**
     * Field subject has mapped Enum values
     *
     * @var array<int>
     */
    protected array $mapSubject = [
        'PERSON' => 1,
        'COMPANY' => 2,
        'SELF_EMPLOYED' => 3,
    ];
}
