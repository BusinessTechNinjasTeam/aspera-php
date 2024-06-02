<?php

namespace AsperaPHP;

use AsperaPHP\API\Model;

class Package extends Model
{
    public string $id;
    public string $node_id; // ID of the Node which acts as Transfer Server for this package
    public string $contents_file_id; // ID of the folder which holds the package contents
    public string $workspace_id;

    public string $name;
    public string $note;

    public bool $complete;
    public bool $expired;
    public bool $sent;
}
