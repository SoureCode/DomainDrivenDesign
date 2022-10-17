<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Area;

use SoureCode\DomainDrivenDesign\Writer\WriterInterface;
use SoureCode\PhpObjectModel\ValueObject\NamespaceName;

interface AreaInterface
{
    public function getDirectory(): string;

    public function getNamespace(): NamespaceName;

    public function write(WriterInterface $writer): self;
}
