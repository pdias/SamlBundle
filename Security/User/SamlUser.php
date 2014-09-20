<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <paulo.dias@ipcb.pt>
 *
 */
namespace SamlBundle\Security\User;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @author: Paulo Dias <paulo.dias@ipcb.pt>
 */
class SamlUser implements UserInterface, EquatableInterface
{
    private $username;
    private $roles;
    private $attributes;

    public function __construct($username, array $roles = array(), array $attributes = array())
    {
        $this->username = $username;
        
        $this->attributes = array();
        foreach($attributes as $key => $attribute){
            if(count($attribute)==1)
                $this->setAttribute($key, $attribute[0]);
            else
                $this->setAttribute($key, $attribute);
        }
        
        $this->roles = array();
        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = new Role($role);
            } elseif (!$role instanceof RoleInterface) {
                throw new \InvalidArgumentException(sprintf('Roles must be an array of strings, or RoleInterface instances, but got %s.', gettype($role)));
            }

            $this->roles[] = $role;
        }
    }

    public function getPassword(){}

    public function getSalt(){}

    public function eraseCredentials(){}

    /**
     * Returns user name.
     *
     * @return string User name
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns user roles.
     *
     * @return array User roles
     */
    public function getRoles()
    {
        return $this->roles;
    }
    
    /**
     * Add user role.
     *
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }
    
    /**
     * Returns the token attributes.
     *
     * @return array The token attributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets the token attributes.
     *
     * @param array $attributes The token attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Returns true if the attribute exists.
     *
     * @param  string  $name  The attribute name
     * @return Boolean true if the attribute exists, false otherwise
     */
    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->attributes);
    }
    
    /**
     * Returns a attribute value.
     *
     * @param string $name The attribute name
     * @return mixed The attribute value
     * @throws \InvalidArgumentException When attribute doesn't exist for this token
     */
    public function getAttribute($name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            throw new \InvalidArgumentException(sprintf('This token has no "%s" attribute.', $name));
        }

        return $this->attributes[$name];
    }
    
    /**
     * Sets a attribute.
     *
     * @param string $name  The attribute name
     * @param mixed  $value The attribute value
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Verify if user is equalt to $user.
     *
     * @param UserInterface $user  User interface
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof SamlUser) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}