<?php
namespace Doctrineum\Float;

use Granam\Strict\Float\StrictFloat;

trait ConvertToFloatTrait
{

    /**
     * @param mixed $valueToConvert
     * @return float
     */
    protected static function convertToFloat($valueToConvert)
    {
        try {
            return (new StrictFloat($valueToConvert, false /* not strict*/))->getValue();
        } catch (\Granam\Strict\Float\Tools\Exceptions\WrongParameterType $exception) {
            // wrapping the exception by local one
            throw new Exceptions\UnexpectedValueToConvert($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

}
