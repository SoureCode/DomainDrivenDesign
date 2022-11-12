<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Tests\Integration\Symfony;

use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\RepositoryType;
use SoureCode\DomainDrivenDesign\DomainDrivenDesign;
use SoureCode\DomainDrivenDesign\Writer\MemoryWriter;
use SoureCode\DomainDrivenDesign\Writer\WriterInterface;
use SoureCode\PhpObjectModel\Type\ClassType;
use Symfony\Component\Uid\Ulid;

class GenerateTest extends AbstractTestCase
{
    private ?WriterInterface $writer = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->writer = new MemoryWriter(__DIR__ . '/');
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->writer->clear();
        $this->writer = null;
    }

    public function testGenerate(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        /**
         * @var DomainDrivenDesign $ddd
         */
        $ddd = $container->get(DomainDrivenDesign::class);

        $bc = $ddd->createBoundingContext('User');
        $domain = $bc->domain();
        $infrastructure = $bc->infrastructure();

        $domain
            ->createModel('User')
            ->addProperty(
                $domain->createValueObject('UserId')
                    ->setType(new ClassType(Ulid::class))
                    ->setPassOrConstruct()
                    ->setColumnName('id'),
                'id',
                false
            )
            ->addProperty(
                $domain->createValueObject('Email')
                    ->setColumnName('email')
                    ->setPass()
            );

        $repository = $domain->createRepository('User');
        $repositoryInterface = $repository->getInterface();

        $infrastructure->createRepository('User', RepositoryType::IN_MEMORY)
            ->getClass()
            ->implement($repositoryInterface);

        $infrastructure->createRepository('User', RepositoryType::DOCTRINE)
            ->getClass()
            ->implement($repositoryInterface);

        $ddd->write($this->writer);

        $files = $this->writer->getFiles();

        self::assertArrayHasKey('src/User/Domain/Model/User.php', $files);
        self::assertArrayHasKey('src/User/Domain/ValueObject/Email.php', $files);
        self::assertArrayHasKey('src/User/Domain/ValueObject/UserId.php', $files);
        self::assertArrayHasKey('src/User/Domain/Repository/UserRepositoryInterface.php', $files);
        self::assertArrayHasKey('src/User/Infrastructure/Repository/UserInMemoryRepository.php', $files);
        self::assertArrayHasKey('src/User/Infrastructure/Repository/UserDoctrineRepository.php', $files);
    }
}
