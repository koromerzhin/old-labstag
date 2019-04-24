<?php

namespace Labstag\Services;

use Labstag\Entity\User;
use Labstag\Entity\OauthConnectUser;
use Symfony\Component\HttpFoundation\Request;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class OauthServices
{
    /**
     * @var array
     */
    protected $configProvider;

    protected function setConfigProvider()
    {
        $this->configProvider = [
            'bitbucket' => [
                'params' => [
                    'urlAuthorize'            => 'https://bitbucket.org/site/oauth2/authorize',
                    'urlAccessToken'          => 'https://bitbucket.org/site/oauth2/access_token',
                    'urlResourceOwnerDetails' => 'https://api.bitbucket.org/2.0/user',
                ],
            ],
            'github'    => [
                'params' => [
                    'urlAuthorize'            => 'https://github.com/login/oauth/authorize',
                    'urlAccessToken'          => 'https://github.com/login/oauth/access_token',
                    'urlResourceOwnerDetails' => 'https://api.github.com/user',
                ],
            ],
            'discord'   => [
                'params'         => [
                    'urlAuthorize'            => 'https://discordapp.com/api/v6/oauth2/authorize',
                    'urlAccessToken'          => 'https://discordapp.com/api/v6/oauth2/token',
                    'urlResourceOwnerDetails' => 'https://discordapp.com/api/v6/users/@me',
                ],
                'scopeseparator' => ' ',
                'scopes'         => [
                    'identify',
                    'email',
                    'connections',
                    'guilds',
                    'guilds.join',
                ],
            ],
            'google'    => [
                'params'         => [
                    'urlAuthorize'            => 'https://accounts.google.com/o/oauth2/v2/auth',
                    'urlAccessToken'          => 'https://www.googleapis.com/oauth2/v4/token',
                    'urlResourceOwnerDetails' => 'https://openidconnect.googleapis.com/v1/userinfo',
                ],
                'redirect'       => 1,
                'scopeseparator' => ' ',
                'scopes'         => [
                    'openid',
                    'email',
                    'profile',
                ],
            ],
        ];
    }

    protected function initProvider($clientName)
    {
        $config = (isset($this->configProvider[$clientName])) ? $this->configProvider[$clientName] : [];
        if (isset($config['redirect'])) {
            $config['params']['redirectUri'] = $this->generateUrl(
                'connect_check',
                [
                    'oauthCode' => $clientName,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        $code                             = strtoupper($clientName);
        $config['params']['clientId']     = getenv('OAUTH_'.$code.'_ID');
        $config['params']['clientSecret'] = getenv('OAUTH_'.$code.'_SECRET');

        $provider = new GenericProviderLib(
            $config['params']
        );
        if (isset($config['scopes'])) {
            $provider->setDefaultScopes($config['scopes']);
        }

        if (isset($config['scopeseparator'])) {
            $provider->setScopeSeparator($config['scopeseparator']);
        }

        return $provider;
    }

    public function setProvider($clientName)
    {
        if (isset($this->configProvider[$clientName])) {
            return $this->initProvider($clientName);
        }
    }

    private function addOauthToUser(string $client, User $user, GenericResourceOwner $userOauth)
    {
        $oauthConnects = $user->getOauthConnectUsers();
        $find = 0;
        foreach($oauthConnects as $oauthConnect)
        { 
            if ($oauthConnect->getName() == $client)
            {
                $find = 1;
                $this->addFlash("warning", "Compte ".$client." déjà associé à un autre utilisateur");
                break;
            }
        }

        if ($find === 0) {
            $oauthConnect = new OauthConnectUser();
            $oauthConnect->setRefuser($user);
            $oauthConnect->setName($client);
            $oauthConnect->setData($userOauth->toArray());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($oauthConnect);
            $entityManager->flush();
            $this->addFlash("success", "Compte ".$client." associé à l'utilisateur ".$user);
        }
    }
}