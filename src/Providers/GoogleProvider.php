<?php 

namespace PhalconSocial\Providers;

use PhalconSocial\Providers\AbstractProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client as HttpClient;
use Phalcon\Config;
use PhalconSocial\User;

class GoogleProvider extends AbstractProvider
{
    protected $scopeSeparator = ' ';

    protected $scopes = ['openid', 'profile', 'email'];

    protected $authorizeUrl = "https://accounts.google.com/o/oauth2/auth";

    protected $tokenUrl = "https://accounts.google.com/o/oauth2/token";

    protected function getTokenFields()
    {
        return arr_add(
            parent::getTokenFields(), 'grant_type', 'authorization_code'
        );
    }
    
    protected function getAuthUrl()
    {
        return $this->authorizeUrl . '?' .http_build_query($this->getFields(), '', '&', $this->encodingType);
    }

    protected function getTokenUrl()
    {
        return $this->tokenUrl;
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://www.googleapis.com/plus/v1/people/me?', [
            'query' => [
                'prettyPrint' => 'false',
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);
        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->set([
            'id' => $user['id'],
            'nickname' => array_get($user ,'nickname'), 
            'name' => $user['displayName'],
            'email' => $user['emails'][0]['value'], 
            'avatar' =>  array_get($user, 'image')['url'],
            'avatar_original' => preg_replace('/\?sz=([0-9]+)/', '', $user['image']['url']),
            'user' => $user
        ]);
    }
}