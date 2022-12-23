<?php

declare(strict_types=1);

use SoureCode\DomainDrivenDesign\BoundingContext\Domain\DomainArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\DomainAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\Model;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectFactory;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\ValueObject\DoctrineValueObject;
use SoureCode\DomainDrivenDesign\Integration\Symfony\Maker\BoundingContextMaker;
use SoureCode\DomainDrivenDesign\Integration\Symfony\Maker\Factory\ServiceFileFactory;
use SoureCode\DomainDrivenDesign\Integration\Symfony\Maker\ModelMaker;
use SoureCode\DomainDrivenDesign\Integration\Symfony\Maker\RepositoryMaker;
use SoureCode\DomainDrivenDesign\Integration\Symfony\Maker\ValueObjectMaker;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    require_once __DIR__ . '/_service_name_generator.php';

    $parameters = $container->parameters();

    $parameters->set(integrationName('repository_interface_class'), "App\\Shared\\Domain\\Repository\\RepositoryInterface");
    $parameters->set(integrationName('doctrine_repository_class'), "App\\Shared\\Infrastructure\\Doctrine\\DoctrineRepository");
    $parameters->set(integrationName('in_memory_repository_class'), "App\\Shared\\Infrastructure\\InMemory\\InMemoryRepository");
    $parameters->set(integrationName('aggregate_root_id_class'), "App\\Shared\\Domain\\ValueObject\\AggregateRootId");

    $services = $container->services();

    $services->set(integrationName('service_file_factory'), ServiceFileFactory::class)
        ->args([
            param('kernel.project_dir'),
        ]);

    $services->set(integrationName('bounding_context_maker'), BoundingContextMaker::class)
        ->args([
            service(name('domain_driven_design')),
            service(integrationName('service_file_factory')),
        ])
        ->tag('maker.command');

    $services->set(integrationName('model_maker'), ModelMaker::class)
        ->args([
            service(name('domain_driven_design')),
            param(integrationName('aggregate_root_id_class')),
        ])
        ->tag('maker.command');

    $services->set(integrationName('repository_maker'), RepositoryMaker::class)
        ->args([
            service(name('domain_driven_design')),
            param(integrationName('repository_interface_class')),
            param(integrationName('doctrine_repository_class')),
            param(integrationName('in_memory_repository_class')),
        ])
        ->tag('maker.command');

    $services->set(integrationName('value_object_maker'), ValueObjectMaker::class)
        ->args([
            service(name('domain_driven_design')),
        ])
        ->tag('maker.command');
};
