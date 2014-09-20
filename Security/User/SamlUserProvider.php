<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace SamlBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlUserProvider implements UserProviderInterface
{
    private $serviceprovider;
    private $autoloadpath;
 
    public function __construct($serviceprovider, $autoloadpath)
    {
        $this->serviceprovider = $serviceprovider;
        $this->autoloadpath = $autoloadpath;
        
        require $this->autoloadpath;
    }
    
    public function loadUserByUsername($username)
    {
        $auth = new \SimpleSAML_Auth_Simple($this->serviceprovider);
        $auth->requireAuth(); 
        
        if ($auth->isAuthenticated()) {
            $attributes = $auth->getAttributes();
            $roles[] = 'ROLE_USER';
            return new SamlUser($username, $roles, $attributes);
        } else {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
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
        return $class === 'SamlBundle\Security\User\SamlUser';
    }
}