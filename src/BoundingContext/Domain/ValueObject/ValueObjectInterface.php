<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject;

use SoureCode\DomainDrivenDesign\Area\AreaFileInterface;
use SoureCode\PhpObjectModel\File\ClassFile;
use SoureCode\PhpObjectModel\Type\AbstractType;

/**
 * @extends AreaFileInterface<ClassFile>
 */
interface ValueObjectInterface extends AreaFileInterface
{
    public function getType(): ?AbstractType;

    /**
     * Returns true if the value param in the constructor will be passed through to the property, false otherwise.
     */
    public function isPass(): bool;

    /**
     * Pass the value param in the constructor to the property.
     */
    public function setPass(): self;

    /**
     * Initiate the property in the constructor.
     */
    public function setConstruct(): self;

    public function isConstruct(): bool;

    /**
     * Pass the value param in the constructor to the property if null initiate the property in the constructor.
     */
    public function setPassOrConstruct(): self;

    public function isPassOrConstruct(): bool;

    public function setType(AbstractType $type): self;
}
