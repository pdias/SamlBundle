<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\DependencyInjection\Loader;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlExtension extends Extension
{
    // You can define what service definitions you want to load
    protected $configFiles = array(
        'services',
        'security',
        'twig'
    );
    
    public function load(array $configs, ContainerBuilder $container)
    {
        // Configuration
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Services
        $fileLocator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new Loader\YamlFileLoader($container, $fileLocator);
        
        foreach ($this->configFiles as $filename) {
            $loader->load($file = sprintf('%s.%s', $filename, 'yml'));
        }
        
        // Set parameters
        if(!isset($config['service_provider'])) {
            throw new \InvalidArgumentException('SamlBundle says "Configured default service provider is not defined."');
        }

        $container->setParameter('saml.service_provider', $config['service_provider']);

        if(!isset($config['autoload_path'])) {
            throw new \InvalidArgumentException('SamlBundle says "Configured default path to SAML autoload is not defined."');
        } else {
            if(!\file_exists($config['autoload_path'])) {
                throw new \InvalidArgumentException('SamlBundle says "Configured default path defines a file that does not exist."');
            }
        }
        
        $container->setParameter('saml.autoload_path', $config['autoload_path']);
        $container->setParameter('saml.authentication_field', $config['authentication_field']);
    }
}
