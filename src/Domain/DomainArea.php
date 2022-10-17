<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Domain;

use SoureCode\DomainDrivenDesign\Area\AbstractSubArea;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\Factory\ModelAreaFactory;
use SoureCode\DomainDrivenDesign\Factory\ValueObjectAreaFactory;
use SoureCode\DomainDrivenDesign\Files\Model;
use SoureCode\DomainDrivenDesign\Files\ValueObject;

final class DomainArea extends AbstractSubArea
{
    private ModelAreaFactory $modelAreaFactory;

    private ValueObjectAreaFactory $valueObjectAreaFactory;

    public function __construct(
        ModelAreaFactory $modelAreaFactory,
        ValueObjectAreaFactory $valueObjectAreaFactory,
        AreaInterface $parent,
        string $name
    ) {
        parent::__construct($parent, $name);

        $this->modelAreaFactory = $modelAreaFactory;
        $this->valueObjectAreaFactory = $valueObjectAreaFactory;
    }

    public function model(): ModelArea
    {
        return $this->createSubArea('Model', $this->modelAreaFactory->create(...));
    }

    /**
     * @return Model[]
     */
    public function getModels(): array
    {
        return $this->model()->getModels();
    }

    public function getModel(string $name): Model
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

    public function createModel(string $name): Model
    {
        return $this->model()->createModel($name);
    }

    public function createValueObject(string $name): ValueObject
    {
        return $this->valueObject()->createValueObject($name);
    }

    public function getValueObject(string $name): ValueObject
    {
        return $this->valueObject()->getValueObject($name);
    }

    public function getValueObjects(): array
    {
        return $this->valueObject()->getValueObjects();
    }

    public function valueObject(): ValueObjectArea
    {
        return $this->createSubArea('ValueObject', $this->valueObjectAreaFactory->create(...));
    }
}
