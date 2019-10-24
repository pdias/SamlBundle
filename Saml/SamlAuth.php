<?php

/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Saml;

use SimpleSAML\Auth\Simple;

/**
 * Handles the class SimpleSAML_Auth_Simple
 *
 * @package    SamlBundle
 * @subpackage Saml
 * @author     Paulo Dias <dias.paulo@gmail.com>
 */
class SamlAuth
{
    protected $provider;
    protected $loginreturn = null;
    protected $logoutreturn = null;
    protected $keeppost = true;
    protected $auth;
    protected $authentication_field = 'mail';

    public function __construct($provider)
    {
        $this->provider = $provider;

        if (\class_exists('\SimpleSAML\Auth\Simple')) {
            $this->auth = new Simple($this->provider);
        } else {
            $this->auth = new \SimpleSAML_Auth_Simple($this->provider);
        }

        $session = \SimpleSAML_Session::getSessionFromRequest();
        $session->cleanup();
    }
    
    public function setProvider($provider)
    {
        $this->provider = $provider;
        if (\class_exists('\SimpleSAML\Auth\Simple')) {
            $this->auth = new Simple($this->provider);
        } else {
            $this->auth = new \SimpleSAML_Auth_Simple($this->provider);
        }
        return $this;
    }
    
    public function getProvider()
    {
        return $this->provider;
    }
    
    public function setLoginReturn($loginreturn)
    {
        $this->loginreturn = $loginreturn;
        return $this;
    }
    
    public function getLoginReturn()
    {
        return $this->loginreturn;
    }
    
    public function setLogoutReturn($logoutreturn)
    {
        $this->logoutreturn = $logoutreturn;
        return $this;
    }
    
    public function getLogoutReturn()
    {
        return $this->logoutreturn;
    }
    
    public function setKeepPost($keeppost)
    {
        $this->keeppost = $keeppost;
        return $this;
    }
    
    public function getKeepPost()
    {
        return $this->keeppost;
    }
    
    public function isAuthenticated()
    {
        return $this->auth->isAuthenticated();
    }
    
    public function requireAuth()
    {
        $options = ['KeepPost' => $this->keeppost];
        if($this->loginreturn) {
            $options = \array_merge($options, ['ReturnTo' => $this->loginreturn]);
        }
        
        $this->auth->requireAuth($options);
    }
    
    public function logout()
    {
        if($this->logoutreturn) {
            $this->auth->logout($this->loginreturn);
        } else {
            $this->auth->logout();
        }
    }
    
    public function getAttributes()
    {
        return $this->auth->getAttributes();
    }
    
    public function getLoginURL()
    {
        return $this->auth->getLoginURL();
    }
    
    public function getLogoutURL()
    {
        return $this->auth->getLogoutURL();
    }
    
    public function getAuthenticationField()
    {
        if($this->isAuthenticated()) {
            if(\array_key_exists($this->authentication_field, $this->getAttributes())) {
                return $this->authentication_field;
            } else {
                throw new \InvalidArgumentException(\sprintf('Your provider must return attribute "%s".', $this->authentication_field));
            }
        }
        
        return $this->authentication_field;
    }
    
    public function setAuthenticationField($authenticationField)
    {
        if($this->isAuthenticated()) {
            if(\array_key_exists($authenticationField, $this->getAttributes())) {
                $this->authentication_field = $authenticationField;
            } else {
                throw new \InvalidArgumentException(\sprintf('Your provider must return attribute "%s".', $this->authentication_field));
            }
        } else {
            $this->authentication_field = $authenticationField;
        }
        
        return $this;
    }
    
    public function getUsername()
    {
        if($this->isAuthenticated()) {
            if(\array_key_exists($this->authentication_field, $this->getAttributes())) {
                $attributes = $this->getAttributes();
                return $attributes[$this->authentication_field][0];
            } else {
                throw new \InvalidArgumentException(\sprintf('Your provider must return attribute "%s".', $this->authentication_field));
            }
        }
    }
}
