<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace SamlBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use SamlBundle\Security\Authentication\Token\SamlUserToken;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;
    protected $serviceprovider;
    protected $dispatcher;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $serviceprovider, EventDispatcherInterface $dispatcher = null)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->dispatcher = $dispatcher;
        $this->serviceprovider = $serviceprovider;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $auth = new \SimpleSAML_Auth_Simple($this->serviceprovider); 
        $auth->requireAuth(); 
        $attributes = $auth->getAttributes();

        $token = new SamlUserToken();

        if(array_key_exists('mail', $attributes)) {
            $token->setUser($attributes['mail'][0]);
        } else {
            throw new \InvalidArgumentException('Your provider must resturn attribute "mail".');
        }

        try {
            $authToken = $this->authenticationManager->authenticate($token);
			
            if ($authToken instanceof TokenInterface ) {
                $this->securityContext->setToken($authToken);
				
                if (null !== $this->dispatcher) {
                    $loginEvent = new InteractiveLoginEvent($request, $authToken);
                    $this->dispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
                }
				
                return;
            } else if ($authToken instanceof Response) {
                return $event->setResponse($authToken);
            }
        } catch (AuthenticationException $e) {
            $response = new Response();
            $response->setStatusCode(403);
            $event->setResponse($response);
        }

        $response = new Response();
        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}