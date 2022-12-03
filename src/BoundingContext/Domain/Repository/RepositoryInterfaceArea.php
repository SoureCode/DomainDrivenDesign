<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository;

use SoureCode\DomainDrivenDesign\Area\AbstractSubAreaFiles;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;

/**
 * @extends AbstractSubAreaFiles<RepositoryInterfaceInterface>
 */
class RepositoryInterfaceArea extends AbstractSubAreaFiles implements RepositoryInterfaceAreaInterface
{
    private RepositoryInterfaceFactoryInterface $repositoryInterfaceFactory;

    public function __construct(
        RepositoryInterfaceFactoryInterface $repositoryInterfaceFactory,
        AreaInterface $parent,
        string $name
    ) {
        parent::__construct($parent, $name);
        $this->repositoryInterfaceFactory = $repositoryInterfaceFactory;
    }

    /**
     * @return RepositoryInterfaceInterface[]
     */
    public function getRepositories(): array
    {
        return $this->getFiles($this->repositoryInterfaceFactory->create(...));
    }

    public function getRepository(string $name): RepositoryInterfaceInterface
    {
        return $this->getFile($name, $this->repositoryInterfaceFactory->create(...));
    }

    public function hasRepository(string $name): bool
    {
        return $this->hasFile($name);
    }

    public function createRepository(string $name): RepositoryInterfaceInterface
    {
        return $this->createFile($name, $this->repositoryInterfaceFactory->create(...));
    }
}
