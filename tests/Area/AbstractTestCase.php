<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Tests\Area;

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use SoureCode\DomainDrivenDesign\Doctrine\DoctrineHelper;
use SoureCode\DomainDrivenDesign\DomainDriveDesign;
use SoureCode\DomainDrivenDesign\Factory\BoundingContextAreaFactory;
use SoureCode\DomainDrivenDesign\Factory\DomainAreaFactory;
use SoureCode\DomainDrivenDesign\Factory\ModelAreaFactory;
use SoureCode\DomainDrivenDesign\Factory\ModelFactory;
use SoureCode\DomainDrivenDesign\Factory\ValueObjectAreaFactory;
use SoureCode\DomainDrivenDesign\Factory\ValueObjectFactory;

abstract class AbstractTestCase extends TestCase
{
    protected ?BoundingContextAreaFactory $boundingContextAreaFactory = null;

    protected ?DomainDriveDesign $ddd = null;

    /**
     * @var (Stub&DomainAreaFactory)|null
     */
    protected ?object $doctrineHelper = null;

    protected ?DomainAreaFactory $domainAreaFactory = null;

    protected ?ModelFactory $modelFactory = null;

    protected ?ValueObjectAreaFactory $valueObjectAreaFactory = null;

    protected ?ValueObjectFactory $valueObjectFactory = null;

    public function setUp(): void
    {
        $this->doctrineHelper = $this->createStub(DoctrineHelper::class);

        $this->doctrineHelper
            ->method('getPotentialTableName')
            ->willReturnCallback(function (string $className) {
                return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
            });

        $this->doctrineHelper
            ->method('isKeyword')
            ->willReturnCallback(function (string $keyword) {
                return in_array(strtolower($keyword), [
                    'user',
                    'group',
                ]);
            });

        $this->valueObjectFactory = new ValueObjectFactory($this->doctrineHelper);
        $this->modelFactory = new ModelFactory($this->doctrineHelper);
        $this->valueObjectAreaFactory = new ValueObjectAreaFactory($this->valueObjectFactory);
        $this->modelAreaFactory = new ModelAreaFactory($this->modelFactory);
        $this->domainAreaFactory = new DomainAreaFactory($this->modelAreaFactory, $this->valueObjectAreaFactory);
        $this->boundingContextAreaFactory = new BoundingContextAreaFactory($this->domainAreaFactory);
        $this->ddd = new DomainDriveDesign($this->boundingContextAreaFactory, __DIR__ . '/../Fixtures', 'App');
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
