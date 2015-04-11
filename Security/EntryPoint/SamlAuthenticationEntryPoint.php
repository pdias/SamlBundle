<?php

/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Security\EntryPoint;

use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface,
    Symfony\Component\HttpFoundation\ParameterBag,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Http\HttpUtils,
    PDias\SamlBundle\Saml\SamlAuth;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    protected $options;
    protected $samlAuth;
    protected $httpUtils;

    /**
     * Constructor
     */
    public function __construct(SamlAuth $samlAuth, HttpUtils $httpUtils, array $options = array())
    {
        $this->samlAuth = $samlAuth;
        $this->httpUtils = $httpUtils;
        $this->options = new ParameterBag($options);
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $this->samlAuth->setLoginReturn($this->httpUtils->generateUri($request, $this->options->get('login_return')));
        $this->samlAuth->requireAuth();
    }
}
