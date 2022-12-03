<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\PhpObjectModel\Model\DeclareModel;
use SoureCode\PhpObjectModel\Model\InterfaceModel;

class RepositoryInterfaceFactory implements RepositoryInterfaceFactoryInterface
{
    /**
     * @var class-string<RepositoryInterfaceInterface>
     */
    private string $className;

    /**
     * @param class-string<RepositoryInterfaceInterface> $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function create(AreaInterface $area, string $name): RepositoryInterfaceInterface
    {
        $repository = new ($this->className)($area, $name);

        $file = $repository->getFile();

        if (!$file->hasDeclare()) {
            $file->setDeclare((new DeclareModel())->setStrictTypes(true));
        }

        if (!$file->hasNamespace()) {
            $file->setNamespace($repository->getNamespace());
        }

        if (!$file->hasInterface()) {
            $file->setInterface(new InterfaceModel($repository->getClassName()));
        }

        return $repository;
    }
}
