<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Tests\Area;

use PHPUnit\Framework\TestCase;
use SoureCode\DomainDrivenDesign\Area\AbstractArea;

class AbstractAreaTest extends TestCase
{
    private AbstractArea $area;

    public function setUp(): void
    {
        $this->area = new class() extends AbstractArea {
            public function __construct()
            {
                parent::__construct(
                    __DIR__,
                    'Foo\\Bar'
                );
            }
        };
    }

    public function testGetNamespaceReturnsNamespace(): void
    {
        self::assertEquals(
            'Foo\\Bar',
            $this->area->getNamespace()->getName()
        );
    }

    public function testGetDirectoryReturnsDirectory(): void
    {
        self::assertEquals(
            __DIR__,
            $this->area->getDirectory()
        );
    }
}
