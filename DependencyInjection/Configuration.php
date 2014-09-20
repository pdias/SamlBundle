<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace SamlBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
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
        ->end();

        return $treeBuilder;
    }
}