<?php
namespace Doctrineum\Float;

use Doctrineum\Scalar\ScalarEnum;
use Granam\Float\Tools\ToFloat;

/**
 * @method float getValue()
 * @method static FloatEnum getEnum($enumValue)
 */
class FloatEnum extends ScalarEnum implements FloatEnumInterface
{
    /**
     * Overloaded parent @see \Doctrineum\Scalar\EnumTrait::convertToEnumFinalValue
     *
     * @param $value
     * @return float
     * @throws \Doctrineum\Float\Exceptions\WrongValueForFloatEnum
     */
    protected static function convertToEnumFinalValue($value)
    {
        try {
            return ToFloat::toFloat($value, true /* strict */);
        } catch (\Granam\Float\Tools\Exceptions\WrongParameterType $exception) {
            throw new Exceptions\WrongValueForFloatEnum($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
