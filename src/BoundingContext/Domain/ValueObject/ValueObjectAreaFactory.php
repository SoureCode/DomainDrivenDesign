<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;

class ValueObjectAreaFactory
{
    /**
     * @var class-string<ValueObjectAreaInterface>
     */
    private string $className;

    private ValueObjectFactoryInterface $valueObjectFactory;

    /**
     * @param class-string<ValueObjectAreaInterface> $className
     */
    public function __construct(string $className, ValueObjectFactoryInterface $valueObjectFactory)
    {
        $this->className = $className;
        $this->valueObjectFactory = $valueObjectFactory;
    }

    public function create(AreaInterface $area, string $name): ValueObjectAreaInterface
    {
        return new ($this->className)($this->valueObjectFactory, $area, $name);
    }
}
