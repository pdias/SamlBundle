<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Event\GetResponseEvent,
    Symfony\Component\Security\Http\Firewall\ListenerInterface,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Core\SecurityContextInterface,
    Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface,
    Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Http\Event\InteractiveLoginEvent,
    Symfony\Component\Security\Http\SecurityEvents,
    Symfony\Component\EventDispatcher\EventDispatcherInterface,
    Symfony\Component\HttpFoundation\ParameterBag,
    PDias\SamlBundle\Security\Authentication\Token\SamlUserToken,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Core\Security,
    Symfony\Component\Security\Http\HttpUtils,
    Psr\Log\LoggerInterface,
    Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface,
    PDias\SamlBundle\Saml\SamlAuth;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlListener implements ListenerInterface
{
    protected $tokenStorage;
    protected $authenticationManager;
    protected $eventDispatcher;
    protected $samlAuth;
    protected $httpUtils;
    protected $logger;
    protected $options;
    
    public function __construct(
            TokenStorageInterface $tokenStorage,
            AuthenticationManagerInterface $authenticationManager,
            HttpUtils $httpUtils,
            EventDispatcherInterface $eventDispatcher = null,
            SamlAuth $samlAuth,
            LoggerInterface $logger = null,
            array $options = array())
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->httpUtils = $httpUtils;
        $this->eventDispatcher = $eventDispatcher;
        $this->samlAuth = $samlAuth;
        $this->logger = $logger;
        $this->options = $options;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        
        try {
            $samlToken = new SamlUserToken();
            $samlToken->setDirectEntry($this->options['direct_entry']);

            $authToken = $this->authenticationManager->authenticate($samlToken);
            if ($authToken instanceof TokenInterface ) {
                $this->onSuccess($request, $authToken);
                return $authToken;
            } else if ($authToken instanceof Response) {
                return $event->setResponse($authToken);
            }
        } catch (\Exception $e) {

            $this->requestSaml($request);

            $token = $this->tokenStorage->getToken();
            if ($token instanceof SamlUserToken/* && $this->providerKey === $token->getProviderKey()*/) {
                 $this->tokenStorage->setToken(null);
            }
            return;
            
            //throw new AuthenticationException('The Saml user could not be retrieved from the session.');
        }
        
        // By default deny authorization
        $response = new Response();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        $event->setResponse($response);
    }
    
    private function onSuccess(Request $request, TokenInterface $token)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('User "%s" has been authenticated successfully', $token->getUsername()));
        }

        $this->tokenStorage->setToken($token);

        $session = $request->getSession();
        $session->remove(Security::AUTHENTICATION_ERROR);
        $session->remove(Security::LAST_USERNAME);

        if (null !== $this->eventDispatcher) {
            $loginEvent = new InteractiveLoginEvent($request, $token);
            $this->eventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
        }
    }
    
    private function requestSaml(Request $request)
    {
        if($this->options['direct_entry'] || $this->httpUtils->checkRequestPath($request, $this->options['check_path']))
        {
            $this->samlAuth->setLoginReturn($this->getReturnUrl($request));
            $this->samlAuth->requireAuth();
        }
    }
    
    private function getReturnUrl(Request $request)
    {
        if($this->options['always_use_default_target_path'] && isset($this->options['default_target_path'])) {
            return $this->httpUtils->generateUri($request, $this->options['default_target_path']);
        }
        
        //return $this->httpUtils->generateUri($request, $this->options['login_return']);
        return $this->httpUtils->generateUri($request, '/');
    }
}
