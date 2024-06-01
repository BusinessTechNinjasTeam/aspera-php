<?php

namespace AsperaPHP\Auth;

class AccessToken
{
    public function __construct(
        protected string $accessToken,
        protected string $tokenType,
        protected int $expiresIn,
        protected string $scope,
    ) {}

    public function __toString(): string
    {
        return $this->accessToken;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['access_token'],
            $data['token_type'],
            $data['expires_in'],
            $data['scope'],
        );
    }
}
