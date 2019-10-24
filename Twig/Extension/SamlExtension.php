<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Twig extension for saml
 *
 * @package    SamlBundle
 * @subpackage Twig\Extension
 * @author     Paulo Dias <dias.paulo@gmail.com>
 */
class SamlExtension extends \Twig_Extension
{
    protected $router;

    /** 
     * @param UrlGeneratorInterface $router
     */ 
    public function __construct(UrlGeneratorInterface $router)
    { 
        $this->router = $router;
    }

    /** 
     * @return array 
     */ 
    public function getFunctions()
    {
        return array( 
            new \Twig_SimpleFunction('samlLoginUrl', [$this, 'getLoginUrl'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('samlLogoutUrl', [$this, 'getLogoutUrl'], ['is_safe' => ['html']]),
        ); 
    } 

    /**
     * @return url 
     */ 
    public function getLoginUrl() 
    {
        return $this->router->generate('saml_login_check');
    }
    
    /**
     * @return url 
     */ 
    public function getLogoutUrl() 
    {
        return $this->router->generate('saml_logout');
    }

    /** 
     * Returns the name of the extension. 
     * 
     * @return string The extension name 
     */ 
    public function getName() 
    { 
        return 'saml_extension'; 
    } 
}
