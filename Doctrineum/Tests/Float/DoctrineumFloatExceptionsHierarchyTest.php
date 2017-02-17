<?php
namespace Doctrineum\Tests\Float;

use Doctrineum\Scalar\ScalarEnum;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class DoctrineumFloatExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @return string
     */
    protected function getTestedNamespace()
    {
        return $this->getRootNamespace();
    }

    /**
     * @return string
     */
    protected function getRootNamespace()
    {
        return str_replace('\Tests', '', __NAMESPACE__);
    }

    /**
     * @return string
     */
    protected function getExternalRootNamespaces()
    {
        $externalRootReflection = new \ReflectionClass(ScalarEnum::class);

        return $externalRootReflection->getNamespaceName();
    }

}