<?php

namespace PhalconSocial\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Phalcon\Config;
use Phalcon\Di\Injectable;

abstract class AbstractProvider extends Injectable
{

    protected $httpClient;

    protected $clientId;

    protected $clientSecret;

    protected $authorizeUrl;

    protected $tokenUrl;

    protected $parameters = [];

    protected $scopes = [];

    protected $scopeSeparator = ',';

    protected $tokenCode;

    protected $encodingType = PHP_QUERY_RFC1738;

    protected $usesState = true;

    protected $stateKey;

    protected $user;

    public function __construct(Config $config)
    {
        $this->clientId = $config->get("client_id");
        $this->redirectUrl = $config->get("redirect_uri");
        $this->clientSecret = $config->get("client_secret");
    }

    protected function usesState(){
        return $this->usesState;
    }

    protected function disableState(){
        $this->usesState = false;
    }

    protected function getState()
    {
        return $this->security->getToken();
    }

    protected function getStateKey()
    {
        return $this->security->getTokenKey();
    }
    
    protected function getFields(){
         $fields = [
            'client_id' => $this->clientId, 
            'redirect_uri' => $this->redirectUrl,
            'scope' => $this->getScopes(),
            'response_type' => 'code',
        ];

        if ($this->usesState()) {
            $fields['state'] = $this->getState();
            $fields['stateKey'] = $this->getStateKey();
        }
        return array_merge($fields, $this->parameters);
    }

    protected function getScopes(){
        return implode($this->scopes, $this->scopeSeparator);
    }

    abstract protected function getAuthUrl();

    abstract protected function getTokenUrl();

    protected function setToken($token){
        $this->tokenCode = $token;
    }

    protected function getTokenFields()
    {
        return [
            'client_id' => $this->clientId, 
            'client_secret' => $this->clientSecret,
            'code' => $this->tokenCode,
            'redirect_uri' => $this->redirectUrl,
        ];
    }

    protected function getHttpClient(){
        if (is_null($this->httpClient)) {
            $this->httpClient = new Client();
        }
        return $this->httpClient;
    }

    public function redirect()
    {
        $this->view->disable();
        $this->response->redirect($this->getAuthUrl(), true);
    }

    protected function checkToken($key, $value)
    {
        return $this->security->checkToken($key, $value);
    }

    public function authorize()
    {

        if($this->usesState()){
            if(!$this->checkToken($this->stateKey, $this->request->getQuery('state'))){
                return false;
            }
        }

        $this->setToken($this->request->getQuery('code'));

        $this->view->disable();

        $response = $this->getAccessTokenResponse($this->tokenCode);

        $user = $this->mapUserToObject($this->getUserByToken($response["access_token"]));
        $user->setToken($response["access_token"])
                    ->setRefreshToken(arr_get($response, "refresh_token"))
                    ->setExpiresIn(arr_get($response, "expires_in"));
        $this->user = $user;

    }

    public function getAccessTokenResponse($code)
    {
        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json'],
            $postKey => $this->getTokenFields($code),
        ]);
        return json_decode($response->getBody(), true);
    }

    public function user()
    {
        return $this->user;

    }
}