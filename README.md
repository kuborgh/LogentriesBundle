Logentries Bundle
==========
[![Build Status][travis-image]][travis-url]
[travis-url]: https://travis-ci.org/kuborgh/logentries-bundle
[travis-image]: https://secure.travis-ci.org/kuborgh/logentries-bundle.svg?branch=master

This bundle integrates logentries.com into your symfony application. It is intended as a monolog handler, but can be used for other purposes, too.

Features
--------
* Logging in JSON Format
* Easy integration vie monolog
* Transport abstraction to use HTTP/TCP/UDP as needed

Installation
------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require kuborgh/logentries-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Kuborgh\LogentriesBundle\LogentriesBundle(),
        );

        // ...
    }

    // ...
}
```

Configuration
-------------

Following configuration variables exist an can be inserted into the config.yml of your project
```yml
kuborgh_logentries:
    handler:
        my_handler:
            # Transport for logging. Supported loggers are only http_guzzle by now 
            transport: http_guzzle

            # Account Key of logentries.com account
            account_key: <logentries account key>
            
            # LogSet
            log_set: my_log_set
            
            # Log channel
            log: my_log
```

To enable monolog logging, add the defined handler(s) to the monolog section
```yml
monolog:
    custom:
        type: service
        id: kuborgh_logentries.handler.my_handler
        level: error
```

Testing
-------
You can run the unitttests with
```bash
$ composer install
$ bin/phpunit
```
The coverage report is saved in the coverage folder.
