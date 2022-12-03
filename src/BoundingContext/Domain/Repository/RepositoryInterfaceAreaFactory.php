<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;

class RepositoryInterfaceAreaFactory
{
    /**
     * @var class-string<RepositoryInterfaceAreaInterface>
     */
    private string $repositoryAreaClass;

    private RepositoryInterfaceFactoryInterface $repositoryFactory;

    /**
     * @param class-string<RepositoryInterfaceAreaInterface> $repositoryAreaClass
     */
    public function __construct(string $repositoryAreaClass, RepositoryInterfaceFactoryInterface $repositoryFactory)
    {
        $this->repositoryAreaClass = $repositoryAreaClass;
        $this->repositoryFactory = $repositoryFactory;
    }

    public function create(AreaInterface $area, string $name): RepositoryInterfaceAreaInterface
    {
        return new ($this->repositoryAreaClass)($this->repositoryFactory, $area, $name);
    }
}
