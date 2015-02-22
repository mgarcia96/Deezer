<?php
namespace SocialiteProviders\Deezer;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['basic_access', 'email'];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://connect.deezer.com/oauth/auth.php', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://connect.deezer.com/oauth/access_token.php';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.deezer.com/user/me?access_token='.$token);
// http://api.deezer.com/2.0/user/me
        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['name'],
            'name'     => $user['firstname'].' '.$user['lastname'],
            'email'    => $user['email'],
            'avatar'   => $user['picture'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildAuthUrlFromBase($url, $state)
    {
        $session = $this->request->getSession();

        return $url.'?'.http_build_query([
            'app_id'        => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'perms'         => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'state'         => $state,
            'response_type' => 'code',
        ], '', '&', $this->encodingType);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken($code)
    {
        $url = $this->getTokenUrl().'?'.http_build_query(
            $this->getTokenFields($code), '', '&', $this->encodingType
        );

        $response = file_get_contents($url);

        return $this->parseAccessToken($response);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return [
            'app_id' => $this->clientId,
            'secret' => $this->clientSecret,
            'code'   => $code,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessToken($body)
    {
        parse_str($body, $result);

        return $result['access_token'];
    }
}