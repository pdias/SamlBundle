<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Security\Firewall;

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

use PDias\SamlBundle\Security\Authentication\Token\SamlUserToken;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;
    protected $serviceprovider;
    protected $authenticationField;
    protected $dispatcher;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $serviceprovider, $authenticationField, EventDispatcherInterface $dispatcher = null)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->dispatcher = $dispatcher;
        $this->serviceprovider = $serviceprovider;
        $this->authenticationField = $authenticationField;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $auth = new \SimpleSAML_Auth_Simple($this->serviceprovider); 
        $auth->requireAuth(); 
        $attributes = $auth->getAttributes();

        $token = new SamlUserToken();

        if(array_key_exists($this->authenticationField, $attributes)) {
            $token->setUser($attributes[$this->authenticationField][0]);
        } else {
            throw new \InvalidArgumentException(sprintf('Your provider must return attribute "%s".', $this->authenticationField));
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
