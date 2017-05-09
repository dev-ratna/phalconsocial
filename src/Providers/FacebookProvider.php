<?php 

namespace PhalconSocial\Providers;

use PhalconSocial\Providers\AbstractProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client as HttpClient;
use Phalcon\Config;
use PhalconSocial\User;

class FacebookProvider extends AbstractProvider
{
	protected $fields = ['name', 'email', 'gender', 'verified', 'link'];

	protected $scopes = ['email'];

	protected $authorizeUrl = "https://www.facebook.com/v2.9/dialog/oauth";

	protected $tokenUrl = "https://graph.facebook.com/v2.9/oauth/access_token";
	
	protected function getAuthUrl()
	{
		return $this->authorizeUrl.'?'.http_build_query($this->getFields(), '', '&', $this->encodingType);
	}

	protected function getTokenUrl()
	{
		return $this->tokenUrl;
	}

	protected function getUserByToken($token)
    {
        $meUrl = 'https://graph.facebook.com/v2.9/me?access_token='.$token.'&fields='.implode(',', $this->fields);
        if (! empty($this->clientSecret)) {
            $appSecretProof = hash_hmac('sha256', $token, $this->clientSecret);
            $meUrl .= '&appsecret_proof='.$appSecretProof;
        }
        $response = $this->getHttpClient()->get($meUrl, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
        return json_decode($response->getBody(), true);
    }

    public function getAccessTokenResponse($code)
    {
        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            $postKey => $this->getTokenFields($code),
        ]);
        $data = [];
        $data = json_decode($response->getBody(), true);
        /*if($data['expires']){


        
        	$expires = $data['expires'];
        	array_forget('expires');
        	if ( ! isset($data['expires_in'])){

             $array['expires_in'] = $expires;
         }}*/
        	return $data;
        
    }

     protected function mapUserToObject(array $user)
    {
        $avatarUrl = 'https://graph.facebook.com/v2.9/'.$user['id'].'/picture';
        return (new User)->set([
            'id' => $user['id'], 
            'nickname' => null, 
            'name' => isset($user['name']) ? $user['name'] : null,
            'email' => isset($user['email']) ? $user['email'] : null, 
            'avatar' => $avatarUrl.'?type=normal',
            'avatar_original' => $avatarUrl.'?width=1920',
            'profileUrl' => isset($user['link']) ? $user['link'] : null,
        ]);
    }
}