<?php

namespace AsperaPHP\Auth;

class AccessToken
{
    protected string $accessToken;
    protected string $tokenType;
    protected int $expiresIn;
    protected string $scope;

    public function __construct(
        string $accessToken,
        string $tokenType,
        int $expiresIn,
        string $scope
    ) {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->expiresIn = $expiresIn;
        $this->scope = $scope;
    }

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
