<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace SamlBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use SamlBundle\Security\Authentication\Token\SamlUserToken;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $cacheDir;

    public function __construct(UserProviderInterface $userProvider, $cacheDir)
    {
        $this->userProvider = $userProvider;
        $this->cacheDir     = $cacheDir;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());
        
        if ($user) {
            $authenticatedToken = new SamlUserToken($user->getRoles());
            $authenticatedToken->setUser($user);
            $authenticatedToken->setAuthenticated(true);
            $authenticatedToken->setAttributes($token->getAttributes());

            return $authenticatedToken;
        }

        throw new AuthenticationException('The SAML authentication failed.');
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof SamlUserToken;
    }
}