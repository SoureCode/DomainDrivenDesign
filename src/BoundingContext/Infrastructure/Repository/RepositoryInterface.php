<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository;

use SoureCode\DomainDrivenDesign\Area\AreaFileInterface;
use SoureCode\PhpObjectModel\File\ClassFile;

/**
 * @extends AreaFileInterface<ClassFile>
 */
interface RepositoryInterface extends AreaFileInterface
{
}
