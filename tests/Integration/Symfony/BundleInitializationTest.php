<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Tests\Integration\Symfony;

use SoureCode\DomainDrivenDesign\DomainDrivenDesign;

class BundleInitializationTest extends AbstractTestCase
{
    public function testInitBundle(): void
    {
        // Boot the kernel.
        $kernel = self::bootKernel();

        // Get the container
        $container = $kernel->getContainer();

        // Test if your services exists
        $service = $container->get(DomainDrivenDesign::class);

        self::assertInstanceOf(DomainDrivenDesign::class, $service);
    }
}
