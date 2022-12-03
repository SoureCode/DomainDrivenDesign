<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Tests\Area;

use PHPUnit\Framework\TestCase;
use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextArea;
use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\DomainArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\DomainAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\Model;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelFactoryInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceFactoryInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObject;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectFactoryInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\InfrastructureArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\InfrastructureAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\DoctrineRepository;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\InMemoryRepository;
use SoureCode\DomainDrivenDesign\DomainDrivenDesign;
use SoureCode\DomainDrivenDesign\DomainDrivenDesignInterface;

abstract class AbstractTestCase extends TestCase
{
    protected ?BoundingContextAreaFactory $boundingContextAreaFactory = null;

    protected ?DomainDrivenDesignInterface $ddd = null;

    protected ?DomainAreaFactory $domainAreaFactory = null;

    protected ?ModelFactoryInterface $modelFactory = null;

    protected ?ValueObjectAreaFactory $valueObjectAreaFactory = null;

    protected ?ValueObjectFactoryInterface $valueObjectFactory = null;

    protected ?RepositoryInterfaceFactoryInterface $domainRepositoryFactory = null;

    protected ?ModelAreaFactory $modelAreaFactory = null;

    protected ?RepositoryInterfaceAreaFactory $domainRepositoryAreaFactory = null;

    protected ?Infrastructure\Repository\RepositoryFactory $doctrineRepositoryFactory = null;

    protected ?Infrastructure\Repository\RepositoryFactory $inMemoryRepositoryFactory = null;

    protected ?InfrastructureAreaFactory $infrastructureAreaFactory = null;

    protected ?Infrastructure\Repository\RepositoryAreaFactory $infrastructureRepositoryAreaFactory = null;

    protected ?Infrastructure\Repository\RepositoryFactory $upstreamRepositoryFactory = null;

    public function setUp(): void
    {
        $this->valueObjectFactory = new ValueObjectFactory(ValueObject::class);
        $this->modelFactory = new ModelFactory(Model::class);
        $this->valueObjectAreaFactory = new ValueObjectAreaFactory(ValueObjectArea::class, $this->valueObjectFactory);
        $this->domainRepositoryFactory = new RepositoryInterfaceFactory(RepositoryInterface::class);
        $this->domainRepositoryAreaFactory = new RepositoryInterfaceAreaFactory(RepositoryInterfaceArea::class, $this->domainRepositoryFactory);
        $this->modelAreaFactory = new ModelAreaFactory(ModelArea::class, $this->modelFactory);
        $this->domainAreaFactory = new DomainAreaFactory(DomainArea::class, $this->modelAreaFactory, $this->valueObjectAreaFactory, $this->domainRepositoryAreaFactory);

        $this->inMemoryRepositoryFactory = new Infrastructure\Repository\RepositoryFactory(InMemoryRepository::class);
        $this->doctrineRepositoryFactory = new Infrastructure\Repository\RepositoryFactory(DoctrineRepository::class);
        $this->infrastructureRepositoryAreaFactory = new Infrastructure\Repository\RepositoryAreaFactory(
            Infrastructure\Repository\RepositoryArea::class,
            $this->inMemoryRepositoryFactory,
            $this->doctrineRepositoryFactory,
        );

        $this->infrastructureAreaFactory = new InfrastructureAreaFactory(InfrastructureArea::class, $this->infrastructureRepositoryAreaFactory);

        $this->boundingContextAreaFactory = new BoundingContextAreaFactory(
            BoundingContextArea::class,
            $this->domainAreaFactory,
            $this->infrastructureAreaFactory
        );
        $this->ddd = new DomainDrivenDesign($this->boundingContextAreaFactory, __DIR__ . '/../Fixtures', 'App');
    }

    public function tearDown(): void
    {
        $this->boundingContextAreaFactory = null;
        $this->ddd = null;
        $this->domainAreaFactory = null;
        $this->modelFactory = null;
        $this->valueObjectAreaFactory = null;
        $this->valueObjectFactory = null;
        $this->domainRepositoryFactory = null;
        $this->modelAreaFactory = null;
        $this->domainRepositoryAreaFactory = null;
        $this->doctrineRepositoryFactory = null;
        $this->inMemoryRepositoryFactory = null;
        $this->infrastructureAreaFactory = null;
        $this->infrastructureRepositoryAreaFactory = null;
    }
}
