<?php

namespace AsperaPHP\Auth;

class Config
{
    public string $clientId;
    public string $userEmail;
    public string $orgSubdomain;
    public string $clientSecret;

    public function __construct(
        string $clientId,
        string $userEmail,
        string $orgSubdomain,
        string $clientSecret
    ) {
        $this->clientId = $clientId;
        $this->userEmail = $userEmail;
        $this->orgSubdomain = $orgSubdomain;
        $this->clientSecret = $clientSecret;
    }

    public static function fromArray($config): self
    {
        return new self(
            $config['CLIENT_ID'],
            $config['USER_EMAIL'],
            $config['ORG_SUBDOMAIN'],
            $config['CLIENT_SECRET'],
        );
    }
}
