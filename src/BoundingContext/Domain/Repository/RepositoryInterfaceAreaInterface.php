<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository;

use SoureCode\DomainDrivenDesign\Area\SubAreaInterface;

interface RepositoryInterfaceAreaInterface extends SubAreaInterface
{
    /**
     * @return RepositoryInterfaceInterface[]
     */
    public function getRepositories(): array;

    public function getRepository(string $name): RepositoryInterfaceInterface;

    public function hasRepository(string $name): bool;

    public function createRepository(string $name): RepositoryInterfaceInterface;
}
