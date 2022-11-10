<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Area;

use Symfony\Component\Filesystem\Path;

/**
 * @template T of AreaInterface
 *
 * @extends AbstractArea<T>
 */
abstract class AbstractSubArea extends AbstractArea implements SubAreaInterface
{
    protected AreaInterface $parent;

    protected string $name;

    public function __construct(AreaInterface $parent, string $name)
    {
        parent::__construct(
            Path::join($parent->getDirectory(), $name),
            $parent->getNamespace()->namespace($name)
        );

        $this->parent = $parent;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
