<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SoureCodeDomainDrivenDesignBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__ . '/config/domain.php');
        $container->import(__DIR__ . '/config/infrastructure.php');
        $container->import(__DIR__ . '/config/bounding_context.php');
        $container->import(__DIR__ . '/config/services.php');

        $container->import(__DIR__ . '/config/doctrine.php');
    }
}
