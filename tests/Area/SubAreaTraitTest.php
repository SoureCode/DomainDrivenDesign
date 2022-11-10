<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Tests\Area;

use Nyholm\NSA;
use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextArea;

class SubAreaTraitTest extends AbstractTestCase
{
    public function testGetSubAreaReturnsSubAreaWhenItsAlreadyCreatedBefore(): void
    {
        NSA::setProperty($this->ddd, 'subAreas', [
            'Foo' => new BoundingContextArea($this->domainAreaFactory, $this->infrastructureAreaFactory, $this->ddd, 'Foo'),
        ]);

        $area = $this->ddd->getBoundingContext('Foo');

        self::assertInstanceOf(BoundingContextArea::class, $area);
        self::assertCount(1, NSA::getProperty($this->ddd, 'subAreas'));
    }

    public function testGetSubAreaReturnsSubAreaWhenDirectoryExist(): void
    {
        $area = $this->ddd->getBoundingContext('Customer');

        self::assertInstanceOf(BoundingContextArea::class, $area);
        self::assertCount(1, NSA::getProperty($this->ddd, 'subAreas'));
    }

    public function testGetSubAreaThrowsExceptionWhenDirectoryDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->ddd->getBoundingContext('Foo');
    }

    public function testHasSubAreaReturnsTrueWhenItsAlreadyCreatedBefore(): void
    {
        NSA::setProperty($this->ddd, 'subAreas', [
            'Foo' => new BoundingContextArea($this->domainAreaFactory, $this->infrastructureAreaFactory, $this->ddd, 'Foo'),
        ]);

        self::assertTrue($this->ddd->hasBoundingContext('Foo'));
    }

    public function testHasSubAreaReturnsTrueWhenDirectoryExists(): void
    {
        self::assertTrue($this->ddd->hasBoundingContext('Customer'));
    }

    public function testHasSubAreaReturnsTrueWhenDirectoryDoesNotExist(): void
    {
        self::assertFalse($this->ddd->hasBoundingContext('Foo'));
    }

    public function testCreateSubAreaReturnsSubAreaWhenItsAlreadyCreatedBefore(): void
    {
        NSA::setProperty($this->ddd, 'subAreas', [
            'Foo' => new BoundingContextArea($this->domainAreaFactory, $this->infrastructureAreaFactory, $this->ddd, 'Foo'),
        ]);

        $area = $this->ddd->createBoundingContext('Foo');

        self::assertInstanceOf(BoundingContextArea::class, $area);
        self::assertCount(1, NSA::getProperty($this->ddd, 'subAreas'));
    }

    public function testCreateSubAreaReturnsSubAreaWhenItsNotCreatedBefore(): void
    {
        $area = $this->ddd->createBoundingContext('Foo');

        self::assertInstanceOf(BoundingContextArea::class, $area);
        self::assertCount(1, NSA::getProperty($this->ddd, 'subAreas'));
    }

    public function testGetSubAreasReturnsSubAreas(): void
    {
        NSA::setProperty($this->ddd, 'subAreas', [
            'Customer' => new BoundingContextArea($this->domainAreaFactory, $this->infrastructureAreaFactory, $this->ddd, 'Customer'),
            'Foo' => new BoundingContextArea($this->domainAreaFactory, $this->infrastructureAreaFactory, $this->ddd, 'Foo'),
        ]);

        $areas = $this->ddd->getBoundingContexts();

        self::assertCount(2, $areas);
    }
}
