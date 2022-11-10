<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure;

use SoureCode\DomainDrivenDesign\Area\AbstractSubArea;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\RepositoryAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\RepositoryAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\RepositoryInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\RepositoryType;

class InfrastructureArea extends AbstractSubArea implements InfrastructureAreaInterface
{
    private RepositoryAreaFactory $repositoryAreaFactory;

    public function __construct(RepositoryAreaFactory $repositoryAreaFactory, AreaInterface $parent, string $name)
    {
        parent::__construct($parent, $name);

        $this->repositoryAreaFactory = $repositoryAreaFactory;
    }

    public function repository(): RepositoryAreaInterface
    {
        return $this->createSubArea('Repository', $this->repositoryAreaFactory->create(...));
    }

    public function getRepositories(RepositoryType $type = null): array
    {
        return $this->repository()->getRepositories($type);
    }

    public function getRepository(string $name, RepositoryType $type): RepositoryInterface
    {
        return $this->repository()->getRepository($name, $type);
    }

    public function hasRepository(string $name, RepositoryType $type): bool
    {
        return $this->repository()->hasRepository($name, $type);
    }

    public function createRepository(string $name, RepositoryType $type): RepositoryInterface
    {
        return $this->repository()->createRepository($name, $type);
    }
}
