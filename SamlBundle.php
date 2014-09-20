<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <paulo.dias@ipcb.pt>
 *
 */
namespace SamlBundle;

use SamlBundle\DependencyInjection\Security\Factory\SamlFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author: Paulo Dias <paulo.dias@ipcb.pt>
 */
class SamlBundle extends Bundle 
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new SamlFactory());
    }
}