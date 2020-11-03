<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\ApiThrottlerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Jalismrs\Symfony\Bundle\ApiThrottlerBundle\DependencyInjection
 */
class Configuration implements
    ConfigurationInterface
{
    public function getConfigTreeBuilder() : TreeBuilder
    {
        $treeBuilder = new TreeBuilder('jalismrs_bundle_api-throttler');
        
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
