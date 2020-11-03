<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\ApiThrottlerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class ApiThrottlerExtension
 *
 * @package Jalismrs\Symfony\Bundle\ApiThrottlerBundle\DependencyInjection
 */
class ApiThrottlerExtension extends
    Extension
{
    /**
     * load
     *
     * @param array                                                   $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     *
     * @throws \Exception
     */
    public function load(
        array $configs,
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
        
        
        $configuration = new Configuration();
        
        $config = $this->processConfiguration(
            $configuration,
            $configs
        );
    }
}
