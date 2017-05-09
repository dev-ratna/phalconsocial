<?php

namespace PhalconSocial;

use Phalcon\Di\Injectable;
use Phalcon\Config;

class SocialLogin extends Injectable{

	protected $provider;

	protected $service;

	protected $providers = [
		'facebook' => \PhalconSocial\Providers\FacebookProvider::class,
		'google' => \PhalconSocial\Providers\GoogleProvider::class
	];

	public function useProvider($provider){	
		$this->setService($provider);
		return $this->getProvider();
	}

	protected function setService($provider){
		$this->service = $provider;
	}

	protected function getProvider(){
		if (is_null($this->provider)) {
            $this->provider = new $this->providers[$this->service]($this->config->social->{$this->service});
        }
		return $this->provider;
	}

	public function __call($method, $params){
		return $this->getProvider()->$method(...$params);
	}
}