<?php

namespace AsperaPHP;

use AsperaPHP\API\Model;

class Node extends Model
{
    /**
     * Specifies the URL where the transfer node is located.
     * Necessary for AoC to make requests via the Node API.
     */
    public string $url;

    /**
     * Access keys are used for authorization when AoC makes a request via the Node API.
     * Each transfer node must have a unique access key.
     */
    public string $access_key;

    /**
     * Host portion of node URL.
     */
    public string $host;

    /**
     * Port portion of the transfer node URL.
     */
    public int $port;
}
