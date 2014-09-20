<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace SamlBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlUserToken extends AbstractToken
{
    public function getCredentials()
    {
        return '';
    }
}