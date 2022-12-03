<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository;

use SoureCode\DomainDrivenDesign\Area\AbstractAreaFile;
use SoureCode\PhpObjectModel\File\InterfaceFile;

/**
 * @extends AbstractAreaFile<InterfaceFile>
 */
class RepositoryInterface extends AbstractAreaFile implements RepositoryInterfaceInterface
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
