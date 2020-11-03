<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsApiThrottlerBundle\DependencyInjection;

use Predis\Client;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * Class JalismrsApiThrottlerExtension
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsApiThrottlerBundle\DependencyInjection
 */
class JalismrsApiThrottlerExtension extends
    ConfigurableExtension
{
    /**
     * loadInternal
     *
     * @param array $mergedConfig
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
        
        $definition = $container->getDefinition(Configuration::CONFIG_ROOT . '.api_throttler');
        
        $definition->replaceArgument(
            '$cap',
            $mergedConfig['cap']
        );
        $definition->replaceArgument(
            '$caps',
            $mergedConfig['caps']
        );
        $definition = $container->getDefinition(Client::class);
    
        $definition->replaceArgument(
            '$parameters',
            $mergedConfig['cap']['parameters']
        );
        $definition->replaceArgument(
            '$options',
            $mergedConfig['caps']['options']
        );
    }
}
