<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Area;

use SoureCode\DomainDrivenDesign\Writer\WriterInterface;
use SoureCode\PhpObjectModel\ValueObject\NamespaceName;
use Symfony\Component\Filesystem\Path;

abstract class AbstractSubArea implements SubAreaInterface
{
    use SubAreaTrait;

    protected AreaInterface $parent;

    protected string $name;

    public function __construct(AreaInterface $parent, string $name)
    {
        $this->parent = $parent;
        $this->name = $name;
    }

    public function getDirectory(): string
    {
        return Path::join($this->parent->getDirectory(), $this->name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNamespace(): NamespaceName
    {
        return $this->parent->getNamespace()->namespace($this->name);
    }

    public function write(WriterInterface $writer): self
    {
        return $this->writeSubAreas($writer);
    }
}
