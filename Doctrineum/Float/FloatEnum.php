<?php
declare(strict_types=1);

namespace Doctrineum\Float;

use Doctrineum\Scalar\ScalarEnum;
use Granam\Float\Tools\ToFloat;

/**
 * @method static FloatEnum getEnum($enumValue)
 */
class FloatEnum extends ScalarEnum implements FloatEnumInterface
{
    public function getValue(): float
    {
        return parent::getValue();
    }

    /**
     * Overloaded parent @see \Doctrineum\Scalar\EnumTrait::convertToEnumFinalValue
     *
     * @param $value
     * @return float
     * @throws \Doctrineum\Float\Exceptions\WrongValueForFloatEnum
     */
    protected static function convertToEnumFinalValue($value): float
    {
        try {
            return ToFloat::toFloat($value, true /* strict */);
        } catch (\Granam\Float\Tools\Exceptions\WrongParameterType $exception) {
            throw new Exceptions\WrongValueForFloatEnum($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
