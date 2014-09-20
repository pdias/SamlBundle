# SamlBundle Getting Started #

Prerequisites
-------------

This version of bundle requires Symfony 2.2+


Installation
------------

Installation is quick 5 steps:

1. Download SamlBundle with composer
2. Enable the bundle
3. Configure SamlBundle
4. Configure application's security.yml
5. Import SamlBundle routing



Step 1: Download SamlBundle with composer
-------------------------------------------

Add to ***composer.json*** to the `require` key

``` yml
    "require" : {
        "pdias/SamlBundle": "~1.0",
    }
```


And run composer to download the bundle with the command

``` bash
    $ php composer.phar update pdias/SamlBundle
```

Composer will install the bundle the the `vendor/pdias/SamlBundle` directory of your project


Step 2: Enable the bundle
-------------------------

Add the ***SamlBundle*** the the kernel of your project:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new pdias\SamlBundle(),
    );
}
```


Step 3: Configure SamlBundle
----------------------------

Now you have to tell to the Bundle what's your service provider and the path to the [simpleSAMLphp](https://simplesamlphp.org/ "simpleSAMLphp Web Page") autoload.

``` yaml
# app/config/config.yml
saml:
    service_provider: 'default-sp'
    autoload_path: '/usr/share/simplesamlphp/lib/_autoload.php'
```


Step 4: Configure application's security.yml
--------------------------------------------

In order Symfony's security component to use the ***SamlBundle*** you must configure it in the `security.yml` file by defining ***encoders***, ***providers*** and a ***firewalls***. Here's a minimal example:

``` yaml
# app/config/security.yml
security:
    encoders:

        SamlBundle\Security\User\SamlUser: plaintext
            
    providers:

        samlservice:
            id: saml.service.user.provider

    firewalls:

        saml_secured:
            provider: samlservice
            saml:
            context: saml_context
            logout:
              path: /logout
              target: /
```

Step 5: Import SamlBundle routing
-----------------------------------

You need to import routing files with default paths for ***SAML*** login.

``` yml
# app/config/routing.yml

saml_bundle:
    resource: "@pdiasSamlBundle/Resources/config/routing.yml"

```