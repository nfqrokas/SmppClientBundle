SmppClientBundle
================

PHP 5 based SMPP client bundle for Symfony2. Forked from https://github.com/kronas/SmppClientBundle, which in turn was forked from https://github.com/onlinecity/php-smpp

For now, it only sends messages.. trasmitter mode

Installation
------------
Add to composer.json

    "require": {
        "nibynool/smpp-client-bundle": "^1.2.0"
    }

Add to AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Nibynool\SmppClientBundle\NibynoolSmppClientBundle(),
        );

        return $bundles;
    }

Add to config.yml

    nibynool_smpp_client:
        host: %smpp_host%
        port: %smpp_port%
        login: %smpp_login%
        password: %smpp_password%
        signature: %smpp_signature%

[More configuration parameters](https://github.com/nibynool/SmppClientBundle/blob/master/Resources/doc/configuration.md)

Usage
-----

    $smpp = $this->get('nibynool_smpp_client.transmitter');

    $smpp->send($phone_number, $message);

*Phone number must be in international format without "+"

**Function "send" return a message ID

License
-------

This bundle is under the [MIT license](https://github.com/nibynool/SmppClientBundle/blob/master/Resources/meta/LICENSE). See the complete license in the bundle:

    Resources/meta/LICENSE
