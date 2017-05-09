# Phalcon Social
Social Login Wrapper for Phalcon (Based on Laravel's Socialite)

## Installation 

```php
composer require dev-ratna/phalcon-social
```

## Configration

the wrapper by deafault searches for 'social' in the main config file.


```php
'social' => [
  'facebook' => [
        'client_id' => 'client-id-here',
        'client_secret' => 'client-secret-here',
        'redirect_uri' => 'url/to/redirect'
    ],
    'google' => [
        'client_id' => '583926489316-gjaoa5cqqtrono7cke07b1cvg2lih9ci.apps.googleusercontent.com',
        'client_secret' => 'fhw5zwhKvR0Vn_YNYT9XYjVL',
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
Usage within Controller
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
    	$user = $this->socialLogin->useProvider('google')->authorize();
    	var_dump($user);
    }
}
