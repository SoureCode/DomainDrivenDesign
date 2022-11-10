<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Tests\Area;

use Nyholm\NSA;
use SoureCode\DomainDrivenDesign\Area\AbstractSubAreaFiles;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\Model;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelInterface;

class AreaFilesTraitTest extends AbstractTestCase
{
    private AbstractSubAreaFiles $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = $this->ddd->getBoundingContext('Customer')->domain()->model();
    }

    public function testGetFileReturnsFileWhenFileExists(): void
    {
        self::assertCount(0, NSA::getProperty($this->sut, 'files'));

        $file = $this->sut->getModel('ExampleA');

        self::assertInstanceOf(ModelInterface::class, $file);
        self::assertCount(1, NSA::getProperty($this->sut, 'files'));
    }

    public function testGetFileReturnsFileWhenAlreadyCreatedBefore(): void
    {
        NSA::setProperty($this->sut, 'files', [
            'ExampleA' => $expected = new Model($this->sut, 'ExampleA'),
        ]);

        $file = $this->sut->getModel('ExampleA');

        self::assertSame($expected, $file);
        self::assertCount(1, NSA::getProperty($this->sut, 'files'));
    }

    public function testGetFileThrowsExceptionWhenFileDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->sut->getModel('Foo');
    }

    public function testHasFileReturnsTrueWhenAlreadyCreatedBefore(): void
    {
        NSA::setProperty($this->sut, 'files', [
            'ExampleA' => new Model($this->sut, 'ExampleA'),
        ]);

        self::assertTrue($this->sut->hasModel('ExampleA'));
    }

    public function testHasFileReturnsTrueWhenFileExists(): void
    {
        self::assertTrue($this->sut->hasModel('ExampleA'));
    }

    public function testHasFileReturnsFalseWhenFileDoesNotExist(): void
    {
        self::assertFalse($this->sut->hasModel('Foo'));
    }

    public function testGetFilesReturnsFilesWhenCreatedBefore(): void
    {
        NSA::setProperty($this->sut, 'files', [
            'Foo' => new Model($this->sut, 'Foo'),
            'Bar' => new Model($this->sut, 'Bar'),
        ]);

        $files = $this->sut->getModels();

        self::assertCount(4, $files);
        self::assertContainsOnlyInstancesOf(ModelInterface::class, $files);
    }

    public function testGetFilesReturnsFilesWhenFilesExists(): void
    {
        $files = $this->sut->getModels();

        self::assertCount(2, $files);
        self::assertContainsOnlyInstancesOf(ModelInterface::class, $files);
    }

    // testCreateFile ReturnsFile WhenDoesNotCreatedBefore
    public function testCreateFileReturnsFileWhenDoesNotCreatedBefore(): void
    {
        self::assertCount(0, NSA::getProperty($this->sut, 'files'));

        $file = $this->sut->createModel('Foo');

        self::assertInstanceOf(ModelInterface::class, $file);
        self::assertCount(1, NSA::getProperty($this->sut, 'files'));
    }

    // testCreateFile ReturnsFile WhenCreatedBefore
    public function testCreateFileReturnsFileWhenCreatedBefore(): void
    {
        NSA::setProperty($this->sut, 'files', [
            'Foo' => $expected = new Model($this->sut, 'Foo'),
        ]);

        $file = $this->sut->createModel('Foo');

        self::assertSame($expected, $file);
        self::assertCount(1, NSA::getProperty($this->sut, 'files'));
    }
}
