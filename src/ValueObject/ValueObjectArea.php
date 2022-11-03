<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\ValueObject;

use SoureCode\DomainDrivenDesign\Area\AbstractSubAreaFiles;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;

final class ValueObjectArea extends AbstractSubAreaFiles
{
    private ValueObjectFactoryInterface $valueObjectFactory;

    public function __construct(ValueObjectFactoryInterface $valueObjectFactory, AreaInterface $parent, string $name)
    {
        parent::__construct($parent, $name);

        $this->valueObjectFactory = $valueObjectFactory;
    }

    /**
     * @return ValueObject[]
     */
    public function getValueObjects(): array
    {
        return $this->getFiles($this->valueObjectFactory->create(...));
    }

    public function getValueObject(string $name): ValueObject
    {
        return $this->getFile($name, $this->valueObjectFactory->create(...));
    }

    public function hasValueObject(string $name): bool
    {
        return $this->hasFile($name);
    }

    public function createValueObject(string $name): ValueObject
    {
        return $this->createFile($name, $this->valueObjectFactory->create(...));
    }

    // getRepositories
    // getRepository
    // hasRepository
    // createRepository

    // getExceptions
    // getException
    // hasException
    // createException
}
