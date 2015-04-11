<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlUserToken extends AbstractToken
{
    private $directEntry = true;
    
    public function getCredentials()
    {
        return '';
    }
    
    public function setDirectEntry($directEntry)
    {
        $this->directEntry = $directEntry;
        return $this;
    }
    
    public function getDirectEntry()
    {
        return $this->directEntry;
    }
    
    public function isDirectEntry()
    {
        return $this->directEntry;
    }
}
