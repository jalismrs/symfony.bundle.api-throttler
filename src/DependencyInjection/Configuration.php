<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsApiThrottlerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsApiThrottlerBundle\DependencyInjection
 */
class Configuration implements
    ConfigurationInterface
{
    public const CONFIG_ROOT = 'jalismrs_api_throttler';
    
    public function getConfigTreeBuilder() : TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::CONFIG_ROOT);
        
        // @formatter:off
        $treeBuilder
            ->getRootNode()
            ->children()
                ->integerNode('cap')
                    ->defaultValue(-1)
                ->end()
                ->arrayNode('caps')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('use_case')
                            ->end()
                            ->scalarNode('identifier')
                            ->end()
                            ->integerNode('cap')
                                ->defaultValue(-1)
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
        
        // @formatter:on
        
        return $treeBuilder;
    }
}
