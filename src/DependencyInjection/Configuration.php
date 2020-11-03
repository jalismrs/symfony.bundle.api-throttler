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
                ->arrayNode('redis')
                    ->info('Redis client configuration')
                    ->isRequired()
                    ->children()
                        ->arrayNode('parameters')
                            ->children()
                                ->scalarNode('host')
                                    ->cannotBeEmpty()
                                    ->isRequired()
                                ->end()
                            ->end()
                            ->ignoreExtraKeys()
                            ->isRequired()
                        ->end()
                        ->arrayNode('options')
                            ->children()
                                ->scalarNode('prefix')
                                    ->cannotBeEmpty()
                                    ->isRequired()
                                ->end()
                            ->end()
                            ->ignoreExtraKeys()
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->integerNode('cap')
                    ->defaultValue(0)
                    ->info('Limit API call failures tu this value. -1 => no limit')
                ->end()
                ->arrayNode('caps')
                    ->info('Limit specific API call failures to this value. -1 => no limit')
                    ->integerPrototype()
                        ->defaultValue(0)
                    ->end()
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('key')
                ->end()
            ->end();
        // @formatter:on
        
        return $treeBuilder;
    }
}
