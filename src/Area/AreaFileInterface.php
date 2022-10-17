<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Area;

use SoureCode\PhpObjectModel\File\ClassFile;

interface AreaFileInterface extends AreaInterface
{
    public function getName(): string;

    public function getFile(): string;

    public function getClassFile(): ClassFile;
}
