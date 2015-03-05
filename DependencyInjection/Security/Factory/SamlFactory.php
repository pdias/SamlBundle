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
    Symfony\Component\DependencyInjection\DefinitionDecorator,
    Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlFactory extends AbstractFactory
{
    public function __construct() 
    { 
        $this->addOption('create_user_if_not_exists', false); 
    } 
    
    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'saml';
    }
    
    protected function getListenerId() {
        return 'saml.security.authentication.listener';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId) {
        $authProviderId = 'security.authentication.provider.saml.'.$id;
        $definition =$container
            ->setDefinition($authProviderId, new DefinitionDecorator('saml.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId));
        
        // with user provider 
        if (isset($config['provider'])) { 
            $definition 
                ->addArgument(new Reference($userProviderId)) 
                ->addArgument(new Reference('security.user_checker')) 
                ->addArgument($config['create_user_if_not_exists']) 
            ; 
        } 

        return $authProviderId;
    }

    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = $this->getListenerId();
        $listener = new DefinitionDecorator($listenerId);
        $listenerId .= '.'.$id;
        $container->setDefinition($listenerId, $listener);
		
        //Logout listener
        $logoutListener = $container->getDefinition('security.logout_listener.'.$id);
        $samlListenerId = 'security.logout.handler.saml';
        
        //Add logout handler
        $container->setDefinition($samlListenerId, new DefinitionDecorator('saml.security.http.logout'));
        $logoutListener->addMethodCall('addHandler', array(new Reference($samlListenerId)));

        return $listenerId;
    }
    
}
