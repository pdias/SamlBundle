<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Reference,
    Symfony\Component\DependencyInjection\ChildDefinition,
    Symfony\Component\Config\Definition\Builder\NodeDefinition,
    Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlFactory extends AbstractFactory
{

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
        parent::addConfiguration($node);

        $builder = $node->children();
        $builder
            ->booleanNode('direct_entry')->defaultTrue()->end()
        ;
    }
    
    protected function getListenerId()
    {
        return 'saml.security.authentication.listener';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $authProviderId = 'security.authentication.provider.saml.'.$id;
        $container
            ->setDefinition($authProviderId, new ChildDefinition('saml.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId));
        
        return $authProviderId;
    }

    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = $this->getListenerId();
        $listener = new ChildDefinition($listenerId);
        $listener->replaceArgument(8, $config);
        $listenerId .= '.'.$id;
        $container->setDefinition($listenerId, $listener);
        
        //Logout listener/handler
        $this->createLogoutHandler($container, $id, $config);
        
        return $listenerId;
    }

    protected function createLogoutHandler($container, $id, $config)
    {
        //Logout listener
        if($container->hasDefinition('security.logout_listener.'.$id)) {
            $logoutListener = $container->getDefinition('security.logout_listener.'.$id);
            $samlListenerId = 'security.logout.handler.saml';

            //Add logout handler
            $container
                ->setDefinition($samlListenerId, new ChildDefinition('saml.security.http.logout'))
                ->replaceArgument(2, \array_intersect_key($config, $this->options));
            $logoutListener->addMethodCall('addHandler', [new Reference($samlListenerId)]);
        }
    }
}
