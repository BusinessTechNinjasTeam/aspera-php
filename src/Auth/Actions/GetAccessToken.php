<?php

namespace AsperaPHP\Auth\Actions;

use AsperaPHP\Auth\AccessToken;
use AsperaPHP\Auth\Config;

class GetAccessToken
{
    protected Config $config;
    protected string $privateKey;

    public function __construct(
        Config $config,
        string $privateKey
    ) {
        $this->config = $config;
        $this->privateKey = $privateKey;
    }

    public function __invoke($scope)
    {
        $jwtHeader = [
            'typ' => 'JWT',
            'alg' => 'RS256'
        ];

        $jwtBody = [
            'iss' => $this->config->clientId,
            'sub' => $this->config->userEmail,
            'aud' => 'https://api.asperafiles.com/api/v1/oauth2/token',
            'nbf' => time() - 3600,
            'exp' => time() + 3600,
        ];

        $payload = sprintf(
            '%s.%s',
            base64_encode(json_encode($jwtHeader)),
            base64_encode(json_encode($jwtBody))
        );

        openssl_sign(
            $payload,
            $signature,
            $this->privateKey,
            OPENSSL_ALGO_SHA256
        );

        $jwt = sprintf('%s.%s', $payload, base64_encode($signature));

        $filesUrl = "https://api.ibmaspera.com/api/v1/oauth2/{$this->config->orgSubdomain}/token";

        $content = [
            'assertion' => $jwt,
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'scope' => $scope,
        ];

        $ch = curl_init($filesUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['content_type' => 'application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->config->clientId}:{$this->config->clientSecret}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if($status < 200 || $status >= 300) {
            throw new \Exception($response);
        }

        return AccessToken::fromArray(json_decode($response, true));
    }
}
