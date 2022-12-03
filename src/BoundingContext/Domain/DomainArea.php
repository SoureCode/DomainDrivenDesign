<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain;

use SoureCode\DomainDrivenDesign\Area\AbstractSubArea;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\Area\SubAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectInterface;

/**
 * @extends AbstractSubArea<SubAreaInterface>
 */
final class DomainArea extends AbstractSubArea implements DomainAreaInterface
{
    private ModelAreaFactory $modelAreaFactory;

    private ValueObjectAreaFactory $valueObjectAreaFactory;

    private RepositoryInterfaceAreaFactory $repositoryAreaFactory;

    public function __construct(
        ModelAreaFactory $modelAreaFactory,
        ValueObjectAreaFactory $valueObjectAreaFactory,
        RepositoryInterfaceAreaFactory $repositoryAreaFactory,
        AreaInterface $parent,
        string $name
    ) {
        parent::__construct($parent, $name);

        $this->modelAreaFactory = $modelAreaFactory;
        $this->valueObjectAreaFactory = $valueObjectAreaFactory;
        $this->repositoryAreaFactory = $repositoryAreaFactory;
    }

    public function model(): ModelAreaInterface
    {
        return $this->createSubArea('Model', $this->modelAreaFactory->create(...));
    }

    /**
     * @return ModelInterface[]
     */
    public function getModels(): array
    {
        return $this->model()->getModels();
    }

    public function getModel(string $name): ModelInterface
    {
        return $this->model()->getModel($name);
    }

    public function hasModel(string $name): bool
    {
        return $this->model()->hasModel($name);
    }

    public function hasValueObject(string $name): bool
    {
        return $this->valueObject()->hasValueObject($name);
    }

    public function createModel(string $name): ModelInterface
    {
        return $this->model()->createModel($name);
    }

    public function createValueObject(string $name): ValueObjectInterface
    {
        return $this->valueObject()->createValueObject($name);
    }

    public function getValueObject(string $name): ValueObjectInterface
    {
        return $this->valueObject()->getValueObject($name);
    }

    /**
     * @return ValueObjectInterface[]
     */
    public function getValueObjects(): array
    {
        return $this->valueObject()->getValueObjects();
    }

    public function valueObject(): ValueObjectAreaInterface
    {
        return $this->createSubArea('ValueObject', $this->valueObjectAreaFactory->create(...));
    }

    public function repository(): RepositoryInterfaceAreaInterface
    {
        return $this->createSubArea('Repository', $this->repositoryAreaFactory->create(...));
    }

    /**
     * @return RepositoryInterfaceInterface[]
     */
    public function getRepositories(): array
    {
        return $this->repository()->getRepositories();
    }

    public function getRepository(string $name): RepositoryInterfaceInterface
    {
        return $this->repository()->getRepository($name);
    }

    public function hasRepository(string $name): bool
    {
        return $this->repository()->hasRepository($name);
    }

    public function createRepository(string $name): RepositoryInterfaceInterface
    {
        return $this->repository()->createRepository($name);
    }
}
