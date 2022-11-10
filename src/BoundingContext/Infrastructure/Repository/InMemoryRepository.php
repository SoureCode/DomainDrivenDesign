<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository;

use SoureCode\DomainDrivenDesign\Area\AbstractAreaFile;
use SoureCode\PhpObjectModel\File\ClassFile;

/**
 * @extends AbstractAreaFile<ClassFile>
 */
class InMemoryRepository extends AbstractAreaFile implements RepositoryInterface
{
}
