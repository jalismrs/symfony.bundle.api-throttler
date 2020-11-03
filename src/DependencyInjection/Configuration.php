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
                    ->info('Limit API call failures tu this value. -1 => no limit')
                    ->defaultValue(0)
                ->end()
                ->arrayNode('caps')
                    ->info('Limit specific API call failures to this value. -1 => no limit')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('key')
                    ->integerPrototype()
                        ->defaultValue(0)
                    ->end()
                ->end()
            ->end();
        
        // @formatter:on
        
        return $treeBuilder;
    }
}
