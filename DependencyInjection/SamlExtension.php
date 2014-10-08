<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // Configuration
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Services
        $fileLocator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new Loader\YamlFileLoader($container, $fileLocator);
        $loader->load('services.yml');
        
        // Set parameters
        if(!isset($config['service_provider'])) {
            throw new \InvalidArgumentException('SamlBundle says "Configured default service provider is not defined."');
        }

        $container->setParameter('saml.service_provider', $config['service_provider']);

        if(!isset($config['autoload_path'])) {
            throw new \InvalidArgumentException('SamlBundle says "Configured default path to SAML autoload is not defined."');
        }
        
        $container->setParameter('saml.autoload_path', $config['autoload_path']);
        $container->setParameter('saml.authentication_field', $config['authentication_field']);
    }
}
