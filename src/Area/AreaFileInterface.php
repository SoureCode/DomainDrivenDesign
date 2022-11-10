<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Area;

use SoureCode\PhpObjectModel\File\AbstractFile;
use SoureCode\PhpObjectModel\Model\ClassModel;
use SoureCode\PhpObjectModel\Model\InterfaceModel;

/**
 * @template-covariant T of AbstractFile
 */
interface AreaFileInterface extends AreaInterface
{
    public function getName(): string;

    public function getClassName(): string;

    public function getFilePath(): string;

    /**
     * @psalm-return T
     */
    public function getFile(): AbstractFile;

    public function getClass(): ClassModel;

    public function getInterface(): InterfaceModel;
}
