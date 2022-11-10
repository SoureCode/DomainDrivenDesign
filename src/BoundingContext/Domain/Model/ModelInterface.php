<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model;

use SoureCode\DomainDrivenDesign\Area\AreaFileInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectInterface;
use SoureCode\PhpObjectModel\File\ClassFile;

/**
 * @extends AreaFileInterface<ClassFile>
 */
interface ModelInterface extends AreaFileInterface
{
    public function addProperty(ValueObjectInterface $valueObject, string $name = null, bool $assign = true): self;
}
