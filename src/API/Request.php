<?php

namespace AsperaPHP\API;

use AsperaPHP\Auth\AccessToken;
use AsperaPHP\Node;
use AsperaPHP\Package;

class Request
{
    public function __construct(
        protected AccessToken $accessToken,
    ) {}

    public function fetch($endpoint, string $class, $id): Model
    {
        return call_user_func(
            [$class, 'fromObject'],
            $this->get("$endpoint/$id")
        );
    }

    public function list($endpoint, string $class): Collection
    {
        $collection = new Collection($this->get($endpoint));

        return $class
            ? $collection->as($class)
            : $collection;
    }

    public function create($endpoint, string $class, array $data): Model
    {
        return call_user_func(
            [$class, 'fromObject'],
            $this->post($endpoint, $data)
        );
    }

    public function update($endpoint, $id, $data): void
    {
        $this->put("$endpoint/$id", $data);
    }

    public function get($endpoint): mixed
    {
        return $this->request($endpoint);
    }

    protected function post($endpoint, $data)
    {
        return $this->request($endpoint, [
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);
    }

    protected function put($endpoint, $data)
    {
        return $this->request($endpoint, [
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);
    }

    protected function request($endpoint, array $options = [])
    {
        $url = "https://api.ibmaspera.com/api/v1/$endpoint";
        $headers = [
            "Accept: application/json",
            "Content-type: application/json",
            "Authorization: Bearer $this->accessToken"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        foreach($options as $option => $value) {
            curl_setopt($ch, $option, $value);
        }

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if($status < 200 || $status >= 300) {
            throw new \Exception($status . $response . json_encode($options));
        }

        return json_decode($response);
    }
}
