<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;

class RepositoryAreaFactory
{
    /**
     * @var class-string<RepositoryAreaInterface>
     */
    private string $repositoryAreaClass;

    private RepositoryFactoryInterface $inMemoryRepositoryFactory;

    private RepositoryFactoryInterface $doctrineRepositoryFactory;

    private RepositoryFactoryInterface $upstreamRepositoryFactory;

    /**
     * @param class-string<RepositoryAreaInterface> $repositoryAreaClass
     */
    public function __construct(
        string $repositoryAreaClass,
        RepositoryFactoryInterface $inMemoryRepositoryFactory,
        RepositoryFactoryInterface $doctrineRepositoryFactory,
        RepositoryFactoryInterface $upstreamRepositoryFactory,
    ) {
        $this->repositoryAreaClass = $repositoryAreaClass;
        $this->inMemoryRepositoryFactory = $inMemoryRepositoryFactory;
        $this->doctrineRepositoryFactory = $doctrineRepositoryFactory;
        $this->upstreamRepositoryFactory = $upstreamRepositoryFactory;
    }

    public function create(AreaInterface $area, string $name): RepositoryAreaInterface
    {
        return new ($this->repositoryAreaClass)(
            $this->inMemoryRepositoryFactory,
            $this->doctrineRepositoryFactory,
            $this->upstreamRepositoryFactory,
            $area,
            $name
        );
    }
}
