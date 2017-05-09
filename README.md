# Phalcon Social
Social Login Wrapper for Phalcon (Based on Laravel's Socialite)

## Installation 

```php
composer require dev-ratna/phalcon-social
```

## Configration

the wrapper by deafault searches for `social` key in the main config file.

the config should use keys `facebook`, `google` or depending on the alias of the provider.


```php
'social' => [
  'facebook' => [
        'client_id' => 'client-id-here',
        'client_secret' => 'client-secret-here',
        'redirect_uri' => 'url/to/redirect'
    ],
    'google' => [
        'client_id' => 'example-id',
        'client_secret' => 'example-secret',
        'redirect_uri' => 'url/to/redirect'
    ]
  ]

```

## Usage

The Service
```php
use PhalconSocial\PhalconSocial;

$di->setShared('socialLogin', function(){
    $socialLogin = new SocialLogin();
    return $socialLogin;

});
```
The routes
```php
$router->add('/social/oauth/redirect', [
	'controller' => 'controller',
	'action' => 'redirect'
]);

$router->add('/social/oauth/login', [
	'controller' => 'controller',
	'action' => 'login'
]);

```
In the Controller
```php
<?php

namespace Namespace\Controllers;


class Controller extends ControllerBase
{

    public function redirectAction()
    {
        $this->socialLogin->useProvider('google')->redirect();
    }

    public function loginAction()
    {
    	$user = $this->socialLogin->useProvider('google')->authorize()->user();
    }
}
