<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface,
    Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Exception\UsernameNotFoundException,
    Symfony\Component\Security\Core\Exception\UnsupportedUserException,
    PDias\SamlBundle\Saml\SamlAuth;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlUserProvider implements UserProviderInterface
{
    protected $samlAuth;
    protected $attributes;
 
    public function __construct(SamlAuth $samlAuth)
    {
        $this->samlAuth = $samlAuth;
        $this->attributes = $this->samlAuth->getAttributes();
    }
    
    public function loadUserByUsername($username)
    {
        if ($this->samlAuth->isAuthenticated()) {
            return new SamlUser($this->samlAuth->getUsername(), array('ROLE_USER'), $this->attributes);
        }

        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SamlUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'PDias\SamlBundle\Security\User\SamlUser';
    }
    
    public function getAttributes()
    {
        return $this->attributes;
    }
}
