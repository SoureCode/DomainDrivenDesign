<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Proxy\Proxy;
use Doctrine\Persistence\AbstractManagerRegistry;
use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextAreaFactory;
use SoureCode\DomainDrivenDesign\Domain\DomainAreaFactory;
use SoureCode\DomainDrivenDesign\DomainDrivenDesign;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\DoctrineHelper;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\Model\DoctrineModelFactory;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\ValueObject\DoctrineValueObject;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\ValueObject\DoctrineValueObjectFactory;
use SoureCode\DomainDrivenDesign\Model\ModelAreaFactory;
use SoureCode\DomainDrivenDesign\Model\ModelFactory;
use SoureCode\DomainDrivenDesign\ValueObject\ValueObjectAreaFactory;
use SoureCode\DomainDrivenDesign\ValueObject\ValueObjectFactory;
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
$namingStrategy = new UnderscoreNamingStrategy(CASE_LOWER, true);
$config->setNamingStrategy($namingStrategy);

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

$valueObjectFactory = new ValueObjectFactory(DoctrineValueObject::class);
$doctrineValueObjectFactory = new DoctrineValueObjectFactory($valueObjectFactory, $doctrineHelper);

$modelFactory = new ModelFactory();
$doctrineModelFactory = new DoctrineModelFactory($modelFactory, $doctrineHelper);

$valueObjectAreaFactory = new ValueObjectAreaFactory($doctrineValueObjectFactory);
$modelAreaFactory = new ModelAreaFactory($doctrineModelFactory);

$domainAreaFactory = new DomainAreaFactory($modelAreaFactory, $valueObjectAreaFactory);
$boundingContextAreaFactory = new BoundingContextAreaFactory($domainAreaFactory);
$ddd = new DomainDrivenDesign($boundingContextAreaFactory, __DIR__ . '/src', 'App');

if (!$ddd->hasBoundingContext('User')) {
    $ddd->createBoundingContext('User');
}

$userBoundingContext = $ddd->getBoundingContext('User');
$domain = $userBoundingContext->domain();

$userModel = $domain->createModel('User');
$userIdValueObject = $domain->createValueObject('UserId');
$userIdValueObject
    ->setType(new ClassType(Ulid::class))
    ->setPassOrConstruct()
    ->setColumnName('id');

$emailValueObject = $domain->createValueObject('Email')
    ->setColumnName('email')
    ->setPass();
$userModel->addProperty($userIdValueObject, 'id', false);
$userModel->addProperty($emailValueObject);

$writer = new FilesystemWriter($filesystem);

$ddd->write($writer);
