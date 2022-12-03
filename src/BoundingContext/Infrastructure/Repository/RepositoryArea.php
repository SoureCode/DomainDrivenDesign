<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository;

use SoureCode\DomainDrivenDesign\Area\AbstractSubAreaFiles;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;

/**
 * @extends AbstractSubAreaFiles<RepositoryInterface>
 */
class RepositoryArea extends AbstractSubAreaFiles implements RepositoryAreaInterface
{
    private RepositoryFactoryInterface $inMemoryRepositoryFactory;

    private RepositoryFactoryInterface $doctrineRepositoryFactory;

    public function __construct(
        RepositoryFactoryInterface $inMemoryRepositoryFactory,
        RepositoryFactoryInterface $doctrineRepositoryFactory,
        AreaInterface $parent,
        string $name
    ) {
        parent::__construct($parent, $name);

        $this->inMemoryRepositoryFactory = $inMemoryRepositoryFactory;
        $this->doctrineRepositoryFactory = $doctrineRepositoryFactory;
    }

    /**
     * @return RepositoryInterface[]
     */
    public function getRepositories(RepositoryType $type = null): array
    {
        if (null === $type) {
            return array_merge(
                $this->getRepositories(RepositoryType::IN_MEMORY),
                $this->getRepositories(RepositoryType::DOCTRINE),
            );
        }

        $factory = $this->getFactory($type);

        return $this->getFiles($factory->create(...), $this->getPattern($type));
    }

    public function getRepository(string $name, RepositoryType $type): RepositoryInterface
    {
        $factory = $this->getFactory($type);

        return $this->getFile($this->getKey($name, $type), $factory->create(...));
    }

    public function hasRepository(string $name, RepositoryType $type): bool
    {
        return $this->hasFile($this->getKey($name, $type));
    }

    public function createRepository(string $name, RepositoryType $type): RepositoryInterface
    {
        $factory = $this->getFactory($type);

        return $this->createFile($this->getKey($name, $type), $factory->create(...));
    }

    private function getFactory(RepositoryType $type): RepositoryFactoryInterface
    {
        return match ($type) {
            RepositoryType::IN_MEMORY => $this->inMemoryRepositoryFactory,
            RepositoryType::DOCTRINE => $this->doctrineRepositoryFactory,
        };
    }

    private function getPattern(RepositoryType $type): string
    {
        return match ($type) {
            RepositoryType::IN_MEMORY => '*InMemoryRepository.php',
            RepositoryType::DOCTRINE => '*DoctrineRepository.php',
        };
    }

    private function getKey(string $name, RepositoryType $type): string
    {
        return match ($type) {
            RepositoryType::IN_MEMORY => $name . 'InMemoryRepository',
            RepositoryType::DOCTRINE => $name . 'DoctrineRepository',
        };
    }
}
