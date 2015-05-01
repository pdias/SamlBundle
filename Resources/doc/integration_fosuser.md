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

``` php
# UserBundle\Security\User\FosBackendSamlUserProvider.php

```


