<?php

namespace AsperaPHP\Auth;

class Config
{
    public function __construct(
        public readonly string $clientId,
        public readonly string $userEmail,
        public readonly string $orgSubdomain,
        public readonly string $clientSecret,
    ) {}

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
