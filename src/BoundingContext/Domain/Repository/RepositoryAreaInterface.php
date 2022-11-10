<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository;

interface RepositoryAreaInterface
{
    /**
     * @return RepositoryInterface[]
     */
    public function getRepositories(): array;

    public function getRepository(string $name): RepositoryInterface;

    public function hasRepository(string $name): bool;

    public function createRepository(string $name): RepositoryInterface;
}
