<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\ApiThrottlerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * Class ApiThrottlerExtension
 *
 * @package Jalismrs\Symfony\Bundle\ApiThrottlerBundle\DependencyInjection
 */
class ApiThrottlerExtension extends
    ConfigurableExtension
{
    /**
     * loadInternal
     *
     * @param array                                                   $mergedConfig
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function loadInternal(
        array $mergedConfig,
        ContainerBuilder $container
    ) : void {
        $fileLocator = new FileLocator(
            __DIR__ . '/../Resources/config'
        );
        
        $yamlFileLoader = new YamlFileLoader(
            $container,
            $fileLocator
        );
        
        $yamlFileLoader->load('services.yaml');
    }
}
