<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <paulo.dias@ipcb.pt>
 *
 */
namespace SamlBundle\Security\Http\Logout;

use Symfony\Component\HttpFoundation\RedirectResponse; 
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface; 
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface; 

/**
 * Handles logging out of Saml when the user logs out of Symfony
 *
 * @package    SamlBundle
 * @subpackage Security\Http\Logout
 * @author     Paulo Dias <paulo.dias@ipcb.pt>
 */
class SamlLogoutHandler implements LogoutHandlerInterface
{
    protected $serviceprovider;

    public function __construct($serviceprovider)
    {
        $this->serviceprovider = $serviceprovider;
    }
        
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $auth = new \SimpleSAML_Auth_Simple($this->serviceprovider); 
        $auth->requireAuth(); 
        
        if($auth->isAuthenticated()) {
            $auth->logout();
        }
    }
}
