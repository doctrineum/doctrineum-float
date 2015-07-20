<?php
namespace Doctrineum\Float;

use Doctrineum\Scalar\Enum;
use Granam\Float\Tools\ToFloat;

/**
 * @method float getEnumValue()
 * @method static FloatEnum getEnum($enumValue)
 */
class FloatEnum extends Enum implements FloatEnumInterface
{

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->getEnumValue();
    }

    /**
     * Overloaded parent @see \Doctrineum\Scalar\EnumTrait::convertToEnumFinalValue
     *
     * @param $value
     * @return float
     */
    protected static function convertToEnumFinalValue($value)
    {
        return ToFloat::toFloat($value);
    }
}
