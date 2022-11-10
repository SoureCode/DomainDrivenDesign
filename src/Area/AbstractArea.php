<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Area;

use SoureCode\DomainDrivenDesign\Writer\WriterInterface;
use SoureCode\PhpObjectModel\ValueObject\NamespaceName;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

/**
 * @template T of AreaInterface
 */
abstract class AbstractArea implements AreaInterface
{
    /**
     * @psalm-var array<string, T>
     */
    protected array $subAreas = [];

    protected string $directory;

    protected NamespaceName $namespace;

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

    /**
     * @psalm-param callable(AreaInterface, string): T $factory
     *
     * @psalm-return T
     */
    protected function getSubArea(string $name, callable $factory): AreaInterface
    {
        if (!isset($this->subAreas[$name])) {
            // check filesystem for existing files
            $path = Path::join($this->getDirectory(), $name);

            if (is_dir($path)) {
                $this->subAreas[$name] = $factory($this, $name);
            } else {
                throw new \InvalidArgumentException(sprintf('Sub area "%s" does not exist.', $name));
            }
        }

        return $this->subAreas[$name];
    }

    protected function hasSubArea(string $name): bool
    {
        if (isset($this->subAreas[$name])) {
            return true;
        }

        $path = Path::join($this->getDirectory(), $name);

        return is_dir($path);
    }

    /**
     * @psalm-param callable(AreaInterface, string): T $factory
     *
     * @psalm-return T[]
     */
    protected function getSubAreas(callable $factory): array
    {
        $finder = (new Finder())
            ->directories()
            ->in(Path::join($this->getDirectory()))
            ->depth(0);

        $items = $this->subAreas;

        foreach ($finder as $directory) {
            $name = $directory->getFilenameWithoutExtension();

            if (isset($items[$name])) {
                $item = $this->subAreas[$name];
            } else {
                $item = $factory($this, $name);
            }

            $items[$name] = $item;
        }

        return $items;
    }

    /**
     * @psalm-param callable(AreaInterface, string): T $factory
     *
     * @psalm-return T
     */
    protected function createSubArea(string $name, callable $factory): AreaInterface
    {
        if (!isset($this->subAreas[$name])) {
            $this->subAreas[$name] = $factory($this, $name);
        }

        return $this->subAreas[$name];
    }

    public function writeSubAreas(WriterInterface $writer): self
    {
        foreach ($this->subAreas as $subArea) {
            $subArea->write($writer);
        }

        return $this;
    }

    public function write(WriterInterface $writer): self
    {
        return $this->writeSubAreas($writer);
    }
}
