<?php

/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Security\Handlers;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response, 
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Http\Logout\LogoutHandlerInterface,
    Symfony\Component\HttpFoundation\ParameterBag,
    Symfony\Component\Security\Http\HttpUtils,
    PDias\SamlBundle\Saml\SamlAuth;

/**
 * Handles logging out of Saml when the user logs out of Symfony
 *
 * @package    SamlBundle
 * @subpackage Security\Handlers
 * @author     Paulo Dias <dias.paulo@gmail.com>
 */
class SamlLogoutHandler implements LogoutHandlerInterface
{
    protected $options;
    protected $samlAuth;
    protected $httpUtils;

    public function __construct(SamlAuth $samlAuth, HttpUtils $httpUtils, array $options = [])
    {
        $this->samlAuth = $samlAuth;
        $this->httpUtils = $httpUtils;
        $this->options = new ParameterBag($options);
    }
        
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        if($this->samlAuth->isAuthenticated()) {
            if(method_exists($response, 'getTargetUrl')) {
                $this->samlAuth->setLogoutReturn($response->getTargetUrl());
            } else {
                $this->samlAuth->setLogoutReturn($this->httpUtils->generateUri($request, $this->options->get('logout_return')));
            }
            $this->samlAuth->logout();
        }
    }
}
