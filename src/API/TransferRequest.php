<?php

namespace AsperaPHP\API;

use AsperaPHP\Node;
use AsperaPHP\Package;

class TransferRequest
{
    public function __construct(
        protected Package $package,
        protected Node $node,
        protected string $authorizationToken,
    ) {}

    public function transfer($homeFolderId, $source)
    {
        $payload = [
            "authentication" => "token",
            "direction" => "send",
            "remote_user" => "xfer",
            "remote_host" => $this->node->host,
            "token" => "Bearer $this->authorizationToken",
            "tags" => [
                "aspera" => [
                    "node" => [
                        "access_key" => $this->node->access_key,
                        "file_id" => $this->package->contents_file_id
                    ],
                    "files" => [
                        "package_id" => $this->package->id,
                        "package_operation" => "upload"
                    ]
                ]
            ],
            "paths" => [
                [
                    "source" => $source
                ]
            ],
            "destination_root_id" => $this->package->contents_file_id,
            "remote_access_key" => $this->node->access_key,
            "source_root_id" => $homeFolderId
        ];

        return $this->post('ops/transfers', $payload);
    }

    public function fetch($endpoint, $id)
    {
        return $this->request("$endpoint/$id");
    }

    protected function post($endpoint, $data)
    {
        return $this->request($endpoint, [
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);
    }

    protected function request($endpoint, array $options = [])
    {
        $url = "{$this->node->url}$endpoint";
        $headers = [
            "Accept: application/json",
            "Content-type: application/json",
            "X-Aspera-AccessKey: {$this->node->access_key}",
            "Authorization: Bearer $this->authorizationToken"
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
            throw new \Exception("$status - $response");
        }

        return json_decode($response);
    }
}

