<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface,
    Symfony\Component\Security\Core\User\UserProviderInterface,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Core\User\UserCheckerInterface,
    PDias\SamlBundle\Security\Authentication\Token\SamlUserToken;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $userChecker;
    private $cacheDir;

    public function __construct(UserProviderInterface $userProvider, UserCheckerInterface $userChecker, $cacheDir)
    {
        $this->userProvider = $userProvider;
        $this->userChecker  = $userChecker;
        $this->cacheDir     = $cacheDir;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $token->getUser();

        if(null !== $user) {
            $this->userChecker->checkPreAuth($user);
        } else {
            $user = $this->userProvider->loadUserByUsername($token->getUsername());
        }
        
        if ($user) {
            $this->userChecker->checkPreAuth($user);

            $authenticatedToken = new SamlUserToken($user->getRoles());
            $authenticatedToken->setUser($user);
            $authenticatedToken->setAuthenticated(true);
            $authenticatedToken->setAttributes($this->userProvider->getAttributes());
            $authenticatedToken->setDirectEntry($token->getDirectEntry());

            return $authenticatedToken;
        }

        throw new AuthenticationException('The SAML authentication failed.');
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof SamlUserToken;
    }
}
