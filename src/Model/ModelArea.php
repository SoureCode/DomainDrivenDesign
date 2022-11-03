<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Model;

use SoureCode\DomainDrivenDesign\Area\AbstractSubAreaFiles;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;

final class ModelArea extends AbstractSubAreaFiles
{
    private ModelFactoryInterface $modelFactory;

    public function __construct(ModelFactoryInterface $modelFactory, AreaInterface $parent, string $name)
    {
        parent::__construct($parent, $name);
        $this->modelFactory = $modelFactory;
    }

    /**
     * @return Model[]
     */
    public function getModels(): array
    {
        return $this->getFiles($this->modelFactory->create(...));
    }

    public function getModel(string $name): Model
    {
        return $this->getFile($name, $this->modelFactory->create(...));
    }

    public function hasModel(string $name): bool
    {
        return $this->hasFile($name);
    }

    public function createModel(string $name): Model
    {
        return $this->createFile($name, $this->modelFactory->create(...));
    }
}
