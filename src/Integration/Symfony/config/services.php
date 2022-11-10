<?php

declare(strict_types=1);

use SoureCode\DomainDrivenDesign\DomainDrivenDesign;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    require_once __DIR__ . '/_service_name_generator.php';

    $parameters = $container->parameters();

    $parameters->set(name('directory'), '%kernel.project_dir%/src');
    $parameters->set(name('namespace'), 'App');

    $services = $container->services();

    $services->set(name('domain_driven_design'), DomainDrivenDesign::class)
        ->public()
        ->args([
            service(boundingContextName('bounding_context_area_factory')),
            param(name('directory')),
            param(name('namespace')),
        ]);

    $services
        ->alias(DomainDrivenDesign::class, name('domain_driven_design'))
        ->public();
};
