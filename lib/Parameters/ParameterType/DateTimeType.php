<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use DateTimeInterface;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Utils\DateTimeUtils;
use Netgen\BlockManager\Validator\Constraint\DateTime as DateTimeConstraint;

/**
 * Parameter type used to store and validate a date and time value. The value of the parameter
 * is a standard \DateTimeInterface object from PHP, however, do note that you can only store
 * the objects which have a time zone type === 3 (meaning, timezone specified via identifier
 * like Europe/Zagreb).
 */
final class DateTimeType extends ParameterType
{
    const DATE_FORMAT = 'Y-m-d H:i:s';
    private static $storageDateFormat = 'Y-m-d H:i:s.u';

    public function getIdentifier()
    {
        return 'datetime';
    }

    public function isValueEmpty(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return !$value instanceof DateTimeInterface;
    }

    public function toHash(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        if (!$value instanceof DateTimeInterface && !is_array($value)) {
            return null;
        }

        if (is_array($value) && (empty($value['datetime']) || empty($value['timezone']))) {
            return null;
        }

        if (!$value instanceof DateTimeInterface) {
            $value = $this->fromHash($parameterDefinition, $value);
        }

        return array(
            'datetime' => $value->format(self::$storageDateFormat),
            'timezone' => $value->getTimezone()->getName(),
        );
    }

    public function fromHash(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return is_array($value) ? DateTimeUtils::createFromArray($value) : null;
    }

    protected function getValueConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return array(
            new DateTimeConstraint(),
        );
    }
}
