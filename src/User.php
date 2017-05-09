<?php 

namespace PhalconSocial;

class User {

	public $token;

    public $refreshToken;

    public $expiresIn;

	public $id;

    public $nickname;

    public $name;

    public $email;

    public $avatar;

    public $user;

    public function set(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }

	public function __get($property) {
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}

	public function __set($property, $value) {
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}
		return $this;
	}
	
	public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }
  
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;
        return $this;
    }
}