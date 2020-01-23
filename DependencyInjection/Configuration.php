<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface,
    Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        if (\method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('saml');
            /** @var ArrayNodeDefinition */
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder = new TreeBuilder();
            /** @var ArrayNodeDefinition */
            $rootNode = $treeBuilder->root('saml');
        }

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
                ->scalarNode('authentication_field')
                    ->defaultValue('mail')
                    ->cannotBeEmpty()
                ->end()
        ->end();

        return $treeBuilder;
    }
}
