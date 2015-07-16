<?php
namespace Doctrineum\Float;

use Granam\Float\Tools\ToFloat;

trait ConvertToFloatEnumFinalValueTrait
{

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
