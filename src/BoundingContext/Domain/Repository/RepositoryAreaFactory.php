<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;

class RepositoryAreaFactory
{
    /**
     * @var class-string<RepositoryAreaInterface>
     */
    private string $repositoryAreaClass;

    private RepositoryFactoryInterface $repositoryFactory;

    /**
     * @param class-string<RepositoryAreaInterface> $repositoryAreaClass
     */
    public function __construct(string $repositoryAreaClass, RepositoryFactoryInterface $repositoryFactory)
    {
        $this->repositoryAreaClass = $repositoryAreaClass;
        $this->repositoryFactory = $repositoryFactory;
    }

    public function create(AreaInterface $area, string $name): RepositoryArea
    {
        return new ($this->repositoryAreaClass)($this->repositoryFactory, $area, $name);
    }
}
