<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Tests\Area;

use PHPUnit\Framework\TestCase;
use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextAreaFactory;
use SoureCode\DomainDrivenDesign\Domain\DomainAreaFactory;
use SoureCode\DomainDrivenDesign\DomainDrivenDesign;
use SoureCode\DomainDrivenDesign\Model\ModelAreaFactory;
use SoureCode\DomainDrivenDesign\Model\ModelFactory;
use SoureCode\DomainDrivenDesign\ValueObject\ValueObject;
use SoureCode\DomainDrivenDesign\ValueObject\ValueObjectAreaFactory;
use SoureCode\DomainDrivenDesign\ValueObject\ValueObjectFactory;

abstract class AbstractTestCase extends TestCase
{
    protected ?BoundingContextAreaFactory $boundingContextAreaFactory = null;

    protected ?DomainDrivenDesign $ddd = null;

    protected ?DomainAreaFactory $domainAreaFactory = null;

    protected ?ModelFactory $modelFactory = null;

    protected ?ValueObjectAreaFactory $valueObjectAreaFactory = null;

    protected ?ValueObjectFactory $valueObjectFactory = null;

    public function setUp(): void
    {
        $this->valueObjectFactory = new ValueObjectFactory(ValueObject::class);
        $this->modelFactory = new ModelFactory();
        $this->valueObjectAreaFactory = new ValueObjectAreaFactory($this->valueObjectFactory);
        $this->modelAreaFactory = new ModelAreaFactory($this->modelFactory);
        $this->domainAreaFactory = new DomainAreaFactory($this->modelAreaFactory, $this->valueObjectAreaFactory);
        $this->boundingContextAreaFactory = new BoundingContextAreaFactory($this->domainAreaFactory);
        $this->ddd = new DomainDrivenDesign($this->boundingContextAreaFactory, __DIR__ . '/../Fixtures', 'App');
    }

    public function tearDown(): void
    {
        $this->boundingContextAreaFactory = null;
        $this->ddd = null;
        $this->doctrineHelper = null;
        $this->domainAreaFactory = null;
        $this->modelFactory = null;
        $this->valueObjectAreaFactory = null;
        $this->valueObjectFactory = null;
    }
}
