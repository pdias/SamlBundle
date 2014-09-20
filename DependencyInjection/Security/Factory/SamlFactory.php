<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace SamlBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
//use SamlBundle\Exception\InvalidConfigurationException;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        //Provider
        $providerId = 'security.authentication.provider.saml.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('saml.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider));

        //Listener
        $listenerId = 'security.authentication.listener.saml.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('saml.security.authentication.listener'));
		
        $logoutListener = $container->getDefinition('security.logout_listener.'.$id);

        $samlListenerId = 'security.logout.handler.saml';
        $samlListener = $container->setDefinition($samlListenerId, new DefinitionDecorator('saml.security.http.logout'));

        $logoutListener->addMethodCall('addHandler', array(new Reference($samlListenerId)));
        
        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'saml';
    }

    public function addConfiguration(NodeDefinition $node)
    {
    }
}