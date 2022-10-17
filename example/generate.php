<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Proxy\Proxy;
use Doctrine\Persistence\AbstractManagerRegistry;
use SoureCode\DomainDrivenDesign\Doctrine\DoctrineHelper;
use SoureCode\DomainDrivenDesign\DomainDriveDesign;
use SoureCode\DomainDrivenDesign\Factory\BoundingContextAreaFactory;
use SoureCode\DomainDrivenDesign\Factory\DomainAreaFactory;
use SoureCode\DomainDrivenDesign\Factory\ModelAreaFactory;
use SoureCode\DomainDrivenDesign\Factory\ModelFactory;
use SoureCode\DomainDrivenDesign\Factory\ValueObjectAreaFactory;
use SoureCode\DomainDrivenDesign\Factory\ValueObjectFactory;
use SoureCode\DomainDrivenDesign\Writer\FilesystemWriter;
use SoureCode\PhpObjectModel\Type\ClassType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Uid\Ulid;

$filesystem = new Filesystem();

if ($filesystem->exists(__DIR__ . '/src')) {
    $filesystem->remove(__DIR__ . '/src');
}

$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$config = ORMSetup::createAnnotationMetadataConfiguration([
    __DIR__ . '/App',
], $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);

// database configuration parameters
$conn = [
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/db.sqlite',
];

class Registry extends AbstractManagerRegistry
{
    /**
     * @param string[] $connections
     * @param string[] $entityManagers
     */
    public function __construct(array $connections, array $entityManagers)
    {
        parent::__construct('ORM', $connections, $entityManagers, 'default', 'default', Proxy::class);
    }

    protected function getService($name)
    {
        return $name;
    }

    protected function resetService(string $name)
    {
        throw new \Exception('Not implemented yet.');
    }
}

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);

$registry = new Registry([
    'default' => $entityManager->getConnection(),
], [
    'default' => $entityManager,
]);

$doctrineHelper = new DoctrineHelper($registry);
$valueObjectFactory = new ValueObjectFactory($doctrineHelper);
$modelFactory = new ModelFactory($doctrineHelper);
$valueObjectAreaFactory = new ValueObjectAreaFactory($valueObjectFactory);
$modelAreaFactory = new ModelAreaFactory($modelFactory);
$domainAreaFactory = new DomainAreaFactory($modelAreaFactory, $valueObjectAreaFactory);
$boundingContextAreaFactory = new BoundingContextAreaFactory($domainAreaFactory);
$ddd = new DomainDriveDesign($boundingContextAreaFactory, __DIR__ . '/src', 'App');

if (!$ddd->hasBoundingContext('User')) {
    $ddd->createBoundingContext('User');
}

$userBoundingContext = $ddd->getBoundingContext('User');
$domain = $userBoundingContext->domain();

$userModel = $domain->createModel('User');
$userIdValueObject = $domain->createValueObject('UserId');
$userIdValueObject
    ->setType(new ClassType(Ulid::class), false)
    ->setColumnName('id');

$emailValueObject = $domain->createValueObject('Email');
$userModel->addProperty($userIdValueObject, 'id', false);
$userModel->addProperty($emailValueObject);

$writer = new FilesystemWriter($filesystem);

$ddd->write($writer);
