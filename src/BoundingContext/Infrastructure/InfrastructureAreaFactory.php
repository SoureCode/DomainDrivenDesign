<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\RepositoryAreaFactory;

class InfrastructureAreaFactory
{
    /**
     * @var class-string<InfrastructureAreaInterface>
     */
    private string $className;

    private RepositoryAreaFactory $repositoryAreaFactory;

    /**
     * @param class-string<InfrastructureAreaInterface> $className
     */
    public function __construct(string $className, RepositoryAreaFactory $repositoryAreaFactory)
    {
        $this->className = $className;
        $this->repositoryAreaFactory = $repositoryAreaFactory;
    }

    public function create(AreaInterface $area, string $name): InfrastructureAreaInterface
    {
        return new ($this->className)($this->repositoryAreaFactory, $area, $name);
    }
}
