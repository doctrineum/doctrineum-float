<?php
namespace Doctrineum\Tests\Float;

use Doctrineum\Scalar\ScalarEnum;
use Granam\Tests\Exceptions\Tools\AbstractExceptionsHierarchyTest;

class ExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace()
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace()
    {
        return str_replace('\Tests', '', __NAMESPACE__);
    }

    protected function getExternalRootNamespaces()
    {
        $externalRootReflection = new \ReflectionClass(ScalarEnum::class);

        return $externalRootReflection->getNamespaceName();
    }

}