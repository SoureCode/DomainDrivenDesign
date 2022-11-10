<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Area;

use SoureCode\DomainDrivenDesign\Writer\WriterInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

/**
 * @template T of AreaFileInterface
 */
abstract class AbstractSubAreaFiles extends AbstractSubArea
{
    /**
     * @var array<string, T>
     */
    protected array $files = [];

    /**
     * @psalm-param callable(AreaInterface, string): T $factory
     *
     * @psalm-return T
     */
    protected function getFile(string $name, callable $factory): AreaFileInterface
    {
        if (!isset($this->files[$name])) {
            $path = Path::join($this->getDirectory(), $name . '.php');

            if (is_file($path)) {
                $this->files[$name] = $factory($this, $name);
            } else {
                throw new \InvalidArgumentException(sprintf('File "%s" does not exist.', $name));
            }
        }

        return $this->files[$name];
    }

    protected function hasFile(string $name): bool
    {
        if (isset($this->files[$name])) {
            return true;
        }

        $path = Path::join($this->getDirectory(), $name . '.php');

        return is_file($path);
    }

    protected function findFiles(string $name = '*.php'): Finder
    {
        return (new Finder())
            ->files()
            ->in(Path::join($this->getDirectory()))
            ->name($name)
            ->depth(0);
    }

    /**
     * @psalm-param callable(AreaInterface, string): T $factory
     *
     * @psalm-return T[]
     */
    protected function getFiles(callable $factory, string $name = '*.php'): array
    {
        $finder = $this->findFiles($name);

        $items = $this->files;

        foreach ($finder as $file) {
            $name = $file->getFilenameWithoutExtension();

            if (isset($items[$name])) {
                $item = $this->files[$name];
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
    protected function createFile(string $name, callable $factory): AreaFileInterface
    {
        if (!isset($this->files[$name])) {
            $this->files[$name] = $factory($this, $name);
        }

        return $this->files[$name];
    }

    public function writeFiles(WriterInterface $writer): self
    {
        foreach ($this->files as $file) {
            $file->write($writer);
        }

        return $this;
    }

    public function write(WriterInterface $writer): self
    {
        $this->writeSubAreas($writer);
        $this->writeFiles($writer);

        return $this;
    }
}
