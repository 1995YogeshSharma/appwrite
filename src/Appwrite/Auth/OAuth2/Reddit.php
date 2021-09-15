<?php

namespace Appwrite\Auth\OAuth2;

use Appwrite\Auth\OAuth2;

// Reference 
// https://github.com/reddit-archive/reddit/wiki/OAuth2

class Reddit extends OAuth2
{
    /**
     * @var array
     */
    protected $user = [];

    /**
     * @var array
     */
    protected $scopes = [
        'identity',
    ];

    /**
     * @return string
     */
    public function getName():string
    {
        return 'reddit';
    }

    /**
     * @return string
     */
    public function getLoginURL():string
    {
        return 'https://www.reddit.com/api/v1/authorize?'. \http_build_query([
            'client_id' => $this->appID,
            'response_type' => 'code',
            'scope' => \implode(' ', $this->getScopes()),
            'state' => \json_encode($this->state),
            // 'duration' => 'temporary',
            'redirect_uri' => $this->callback,
        ]);
    }

    /**
     * @param string $code
     *
     * @return string
     */
    public function getAccessToken(string $code):string
    {
        $headers = [
            'Authorization: Basic ' . \base64_encode($this->appID . ':' . $this->appSecret)
        ];

        $accessToken = $this->request(
            'POST',
            'https://www.reddit.com/api/v1/access_token',
            $headers,
            \http_build_query([
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->callback,
                'code' => $code
            ])
        );

        $output = [];

        \parse_str($accessToken, $output);

        if (isset($output['access_token'])) {
            return $output['access_token'];
        }

        return '';
    }

    /**
     * @param $accessToken
     *
     * @return string
     */
    public function getUserID(string $accessToken):string
    {
        $user = $this->getUser($accessToken);

        if (isset($user['id'])) {
            return $user['id'];
        }

        return '';
    }

    /**
     * @param $accessToken
     *
     * @return string
     */
    public function getUserEmail(string $accessToken):string
    {
        return '';
    }

    /**
     * @param $accessToken
     *
     * @return string
     */
    public function getUserName(string $accessToken):string
    {
        $user = $this->getUser($accessToken);

        if (isset($user['name'])) {
            return $user['name'];
        }

        return '';
    }

    /**
     * @param string $accessToken
     *
     * @return array
     */
    protected function getUser(string $accessToken)
    {
        // TODO: Implement getUser() method.
    }
}