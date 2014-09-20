<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <paulo.dias@ipcb.pt>
 *
 */
namespace SamlBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * @author: Paulo Dias <paulo.dias@ipcb.pt>
 */
class SamlUserToken extends AbstractToken
{
    public function getCredentials()
    {
        return '';
    }
}