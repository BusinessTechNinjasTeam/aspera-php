<?php

namespace AsperaPHP;

use AsperaPHP\API\Request;
use AsperaPHP\API\TransferRequest;
use AsperaPHP\Auth\AccessToken;
use AsperaPHP\Auth\Actions\GetAccessToken;
use AsperaPHP\Auth\Config;

class Aspera
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

    public function requestWithScope(string $scope)
    {
        return new Request($this->getAccessTokenWithScope($scope));
    }

    public function transferRequestForPackage(Package $package, Node $node): TransferRequest
    {
        return new TransferRequest(
            $package,
            $node,
            $this->getAccessTokenWithScope("node.$node->access_key:user:all"),
        );
    }

    protected function getAccessTokenWithScope(string $scope): AccessToken
    {
        return (new GetAccessToken($this->config, $this->privateKey))($scope);
    }
}
