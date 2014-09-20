<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <paulo.dias@ipcb.pt>
 *
 */
namespace SamlBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author: Paulo Dias <paulo.dias@ipcb.pt>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('saml');

        $rootNode
            ->children()
                ->scalarNode('service_provider')
                    ->defaultValue('default-sp')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('autoload_path')
                    ->defaultValue('/usr/share/simplesamlphp/lib/_autoload.php')
                    ->cannotBeEmpty()
                ->end()
            /*->arrayNode('admin_users')
                ->requiresAtLeastOneElement()
                ->prototype('array')
                    ->children()
                        ->scalarNode('email')
                        ->isRequired(true)
                    ->end()
                    ->booleanNode('is_active')
                        ->defaultValue(true)
                    ->end()
                ->end()
            ->end()*/
        ->end();

        return $treeBuilder;
    }
}