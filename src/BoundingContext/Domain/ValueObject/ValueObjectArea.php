<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject;

use SoureCode\DomainDrivenDesign\Area\AbstractSubAreaFiles;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;

/**
 * @extends AbstractSubAreaFiles<ValueObjectInterface>
 */
class ValueObjectArea extends AbstractSubAreaFiles implements ValueObjectAreaInterface
{
    private ValueObjectFactoryInterface $valueObjectFactory;

    public function __construct(ValueObjectFactoryInterface $valueObjectFactory, AreaInterface $parent, string $name)
    {
        parent::__construct($parent, $name);

        $this->valueObjectFactory = $valueObjectFactory;
    }

    /**
     * @return ValueObjectInterface[]
     */
    public function getValueObjects(): array
    {
        return $this->getFiles($this->valueObjectFactory->create(...));
    }

    public function getValueObject(string $name): ValueObjectInterface
    {
        return $this->getFile($name, $this->valueObjectFactory->create(...));
    }

    public function hasValueObject(string $name): bool
    {
        return $this->hasFile($name);
    }

    public function createValueObject(string $name): ValueObjectInterface
    {
        return $this->createFile($name, $this->valueObjectFactory->create(...));
    }

    // getExceptions
    // getException
    // hasException
    // createException
}
