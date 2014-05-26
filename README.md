**RESOURCE-PROXY LIBRARY**
======================

This library provides an easy way to get resources from a remote resource source (e.g. emails, images, documents, etc.).

It is based on ZendFramework2. The following ZF2 modules are currently used 
(take a look at the composer.json for a full overview about all dependencies):

```js
{
    "require": {
        "zendframework/zend-mail": "2.*"
    }
}
```

**INSTALLATION**
----------------

Installation is a quick 3 step process:

1. Download ResourceProxy using composer
2. Configure your sources
3. Generate your ResourceProxy implementation

### Step 1: Download ResourceProxy using composer

Add RemoteHost in your composer.json:

```js
{
    "require": {
        "mslib/resource-proxy": "dev-master"
    }
}
```

Now tell composer to download the library by running the command:

``` bash
$ php composer.phar update mslib/resource-proxy
```

Composer will install the library to your project's `vendor/mslib/resource-proxy` directory.

### Step 2: Configure your sources

Now that the ResourceProxy library is available in your project (through composer, for example),
you need to configure all required Sources that your application needs.

In order to do that, you have to create a PHP file containing an array with a given set of configuration 
keys and values. You will find here below the structure of such a file.

``` php
<?php

return array(
    'global' => array(
        'host'          => '', // (Not required)
        'port'          => '', // (Not required)
        'ssl'           => '', // (Not required) Possible values: 'SSL',
        'output_folder' => '',
    ),
    'sources' => array(
        'source-name' => array(
            'type'              => 'imap|sftp|ftp', // (REQUIRED) the resource type. Possible values: 'imap|sftp|ftp'
            'connection'           => array(
                'host'          => '', // (If not specified, we check the global.host variable)
                'port'          => '', // (If not specified, we check the global.port variable)
                'ssl'           => '', // (If not specified, we check the global.ssl variable) Possible values: 'SSL'
                'username'      => '', // (REQUIRED)
                'password'      => '', // (REQUIRED)
                'filter'        => array( // The filter list could be different for each source type
                    'message_status' => 'unread_only', // FOR IMAP TYPE ONLY
                    'folder'         => '', // FOR IMAP TYPE ONLY. If not specified, INBOX will be selected
                    'start_date'     => 'yesterday|last_week|last_month', // FOR FTP/SFTP TYPE ONLY
                )
            ),
        ),
    ),
);
```

The resources will be wrapped in an appropriate implementation of the library interface *'Msl\ResourceProxy\Resource\ResourceInterface'*.
For example, for the 'imap' type, each resource will be wrapped into a *'Msl\ResourceProxy\Resource\ImapMessage'* object instance.

### Step3: Generate your ResourceProxy implementation

Now that you have configured all required Sources, you need to create, in your project, a class that extends
the base abstract class *'Msl\ResourceProxy\Proxy\AbstractProxy'*, defined in the library RemoteHost.

The default class constructor requires two parameters: 

* ***$proxyName***: the name of the proxy to be used in logs, exceptions, etc. (e.g. 'MY_PROXY'); the default value is defined in the class constant Msl\ResourceProxy\Proxy\AbstractProxy::PROXY_NAME; to override this value, you either pass a valid string as the first parameter of the class constructor, or you redefine the constant PROXY_NAME in your child class;
* ***$config***: the array containing the configuration defined at step 2;
