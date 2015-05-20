<?php
namespace Doctrineum\Float;

trait FloatEnumTrait
{

    use ConvertToFloatTrait;

    /**
     * Overloads "parent" @see \Doctrineum\Scalar\Enum::convertToEnumFinalValue
     *
     * @param mixed $enumValue
     * @return float
     */
    protected static function convertToEnumFinalValue($enumValue)
    {
        return self::convertToFloat($enumValue);
    }

}
