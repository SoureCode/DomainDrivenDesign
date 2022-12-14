<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository;

use SoureCode\DomainDrivenDesign\Area\SubAreaInterface;

interface RepositoryAreaInterface extends SubAreaInterface
{
    /**
     * @return RepositoryInterface[]
     */
    public function getRepositories(RepositoryType $type = null): array;

    public function getRepository(string $name, RepositoryType $type): RepositoryInterface;

    public function hasRepository(string $name, RepositoryType $type): bool;

    public function createRepository(string $name, RepositoryType $type): RepositoryInterface;
}
