<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository;

use SoureCode\DomainDrivenDesign\Area\AbstractSubAreaFiles;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;

class RepositoryArea extends AbstractSubAreaFiles implements RepositoryAreaInterface
{
    private RepositoryFactoryInterface $repositoryFactory;

    public function __construct(RepositoryFactoryInterface $repositoryFactory, AreaInterface $parent, string $name)
    {
        parent::__construct($parent, $name);
        $this->repositoryFactory = $repositoryFactory;
    }

    /**
     * @return RepositoryInterface[]
     */
    public function getRepositories(): array
    {
        return $this->getFiles($this->repositoryFactory->create(...));
    }

    public function getRepository(string $name): RepositoryInterface
    {
        return $this->getFile($name, $this->repositoryFactory->create(...));
    }

    public function hasRepository(string $name): bool
    {
        return $this->hasFile($name);
    }

    public function createRepository(string $name): RepositoryInterface
    {
        return $this->createFile($name, $this->repositoryFactory->create(...));
    }
}
