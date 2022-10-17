<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Area;

use SoureCode\DomainDrivenDesign\Writer\WriterInterface;
use SoureCode\PhpObjectModel\ValueObject\NamespaceName;

abstract class AbstractArea implements AreaInterface
{
    use SubAreaTrait;

    private string $directory;

    private NamespaceName $namespace;

    public function __construct(string $directory, NamespaceName|string $namespace)
    {
        $this->directory = $directory;
        $this->namespace = is_string($namespace) ? new NamespaceName($namespace) : $namespace;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function getNamespace(): NamespaceName
    {
        return $this->namespace;
    }

    public function write(WriterInterface $writer): self
    {
        return $this->writeSubAreas($writer);
    }
}
