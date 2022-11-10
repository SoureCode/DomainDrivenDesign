<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository;

use SoureCode\DomainDrivenDesign\Area\AbstractAreaFile;
use SoureCode\PhpObjectModel\File\InterfaceFile;

/**
 * @extends AbstractAreaFile<InterfaceFile>
 */
class Repository extends AbstractAreaFile implements RepositoryInterface
{
    public function getClassName(): string
    {
        return parent::getClassName() . 'RepositoryInterface';
    }

    public function getFileTypeClassName(): string
    {
        return InterfaceFile::class;
    }
}
