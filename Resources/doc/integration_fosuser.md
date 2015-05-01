Integration with FOSUserBundle
------------------------------

If you still want to use the ***FOSUserBundle*** form login, you must configure it in the `security.yml` file by defining ***encoders***, ***providers*** and a ***firewalls***.

``` yaml
# app/config/security.yml

security:
    encoders:

        Symfony\Component\Security\Core\User\User:
            algorithm: sha1
            encode_as_base64: false
            iterations: 1

        FOS\UserBundle\Model\UserInterface: sha512

        PDias\SamlBundle\Security\User\SamlUser: plaintext

    providers:

        fos_userbundle:
            id: fos_user.user_provider.username

        backend_samlservice:
            id: saml.backend.fosuser.provider

    firewalls:

        admin:
            switch_user: true
            pattern: /admin(.*)
            form_login:
                provider: fos_userbundle
                login_path: /admin/login
                check_path: /admin/login-check
                failure_path: /admin/login
                default_target_path: /admin
                use_forward: false
                use_referer: true
            saml:
                provider: backend_samlservice
                direct_entry: false
                login_path: /admin/login-saml
                check_path: /admin/login-check-saml
                default_target_path: /admin/dashboard
                always_use_default_target_path: true
            logout:
                path:   /admin/logout
                target: /admin/login
            anonymous: true
```

The option ***direct_entry*** in SAML must be set to ***false***. ***By default this option is true*** and if it is ***true*** it goes directly to the SAML login window.

Now we need to add a service (*saml.backend.fosuser.provider*) in the ***custom user provider***.

``` xml
# UserBundle\Resources\config\services.xml

    <service id="saml.backend.fosuser.provider" class="UserBundle\Security\User\FosBackendSamlUserProvider">
        <argument type="service" id="samlauth.service"/>
        <argument type="service" id="fos_user.user_manager"/>
    </service>


```


``` php
# UserBundle\Security\User\FosBackendSamlUserProvider.php

namespace UserBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface,
    Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Exception\UsernameNotFoundException,
    Symfony\Component\Security\Core\Exception\UnsupportedUserException,
    PDias\SamlBundle\Security\User\SamlUser,
    PDias\SamlBundle\Saml\SamlAuth;

class FosBackendSamlUserProvider implements UserProviderInterface
{
    protected $samlAuth;
    protected $userManager;
 
    public function __construct(SamlAuth $samlAuth, $userManager)
    {
        $this->samlAuth = $samlAuth;
        $this->userManager = $userManager;
    }
    
    public function loadUserByUsername($username)
    {
        if ($this->samlAuth->isAuthenticated()) {
            if($user = $this->findUserBySamlId($this->samlAuth->getUsername())) {
                $samlUser = new SamlUser($this->samlAuth->getUsername(), $user->getRoles(), $this->samlAuth->getAttributes());

                return $samlUser;
            }
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
        return $this->userProvider->supportsClass($class);
    }
    
    public function findUserBySamlId($samlId)
    {
        return $this->userManager->findUserBy(array('samlId' => $samlId));
    }
}
```


