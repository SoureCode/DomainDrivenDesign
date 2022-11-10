<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository;

use SoureCode\DomainDrivenDesign\Area\AreaFileInterface;
use SoureCode\PhpObjectModel\File\InterfaceFile;

/**
 * @extends AreaFileInterface<InterfaceFile>
 */
interface RepositoryInterface extends AreaFileInterface
{
}
