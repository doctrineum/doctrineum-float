<?php
namespace Doctrineum\Float;

use Doctrineum\Scalar\Enum;

class FloatEnum extends Enum
{
    use FloatEnumTrait;

    /**
     * Consider about use of @see Enum::getEnum to obtain same instance for same value.
     *
     * @param float $floatValue
     */
    public function __construct($floatValue)
    {
        parent::__construct(static::convertToEnumFinalValue($floatValue));
    }

}
