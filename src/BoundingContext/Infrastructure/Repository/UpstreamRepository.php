<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository;

use SoureCode\DomainDrivenDesign\Area\AbstractAreaFile;
use SoureCode\PhpObjectModel\File\ClassFile;

/**
 * @extends AbstractAreaFile<ClassFile>
 */
class UpstreamRepository extends AbstractAreaFile implements RepositoryInterface
{
}
