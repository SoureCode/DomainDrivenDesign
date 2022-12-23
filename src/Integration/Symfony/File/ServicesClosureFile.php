<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony\File;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use SoureCode\DomainDrivenDesign\Integration\Symfony\Model\ServicesSetCallModel;
use SoureCode\DomainDrivenDesign\Integration\Symfony\Model\ServicesSetModel;
use SoureCode\DomainDrivenDesign\Integration\Symfony\Visitor\ServicesSetsVisitor;
use SoureCode\PhpObjectModel\File\ClosureFile;
use SoureCode\PhpObjectModel\ValueObject\ClassName;

class ServicesClosureFile extends ClosureFile
{
    /**
     * @return ServicesSetModel[]
     */
    public function findServicesSets(): array
    {
        $visitor = new ServicesSetsVisitor();

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->statements);

        $servicesSetCalls = $visitor->getServiceSetCalls();
        $servicesSets = [];

        foreach ($servicesSetCalls as $servicesSetCall) {
            $model = new ServicesSetModel(
                $this, $servicesSetCall['node'], array_map(function (Node\Expr\MethodCall $node) {
                    $model = new ServicesSetCallModel($node);
                    $model->setFile($this);

                    return $model;
                }, $servicesSetCall['calls'])
            );

            $model->setFile($this);

            $servicesSets[] = $model;
        }

        return $servicesSets;
    }

    public function hasServiceSet(ClassName $id): bool
    {
        $servicesSets = $this->findServicesSets();

        foreach ($servicesSets as $servicesSet) {
            $compareId = $servicesSet->getId();
            if ($id->isSame($compareId)) {
                return true;
            }
        }

        return false;
    }

    public function getServiceSet(ClassName $id): ServicesSetModel
    {
        $servicesSets = $this->findServicesSets();

        foreach ($servicesSets as $servicesSet) {
            if ($id->isSame($servicesSet->getId())) {
                return $servicesSet;
            }
        }

        throw new \RuntimeException(sprintf('Could not find service set with id "%s".', $id->getName()));
    }

    public function setServiceCall(ClassName $id, ClassName $class = null): ServicesSetModel
    {
        if ($this->hasServiceSet($id)) {
            $servicesSet = $this->getServiceSet($id);

            if (null !== $class && !$class->isSame($servicesSet->getServiceClass())) {
                $servicesSet->class($class);
            }

            return $servicesSet;
        }

        $useName = $this->resolveUseName($id);

        $args = [
            new Node\Arg(
                new Node\Expr\ClassConstFetch(
                    $useName,
                    'class'
                )
            ),
        ];

        $servicesSet = new ServicesSetModel($this, new Node\Expr\MethodCall(new Node\Expr\Variable('services'), 'set', $args));
        $servicesSet->setFile($this);

        $this->getClosure()->addStatement($servicesSet->getNode());

        if (null !== $class) {
            $servicesSet->class($class);
        }

        return $servicesSet;
    }
}
