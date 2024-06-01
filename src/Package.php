<?php

namespace AsperaPHP;

use AsperaPHP\API\Model;

class Package extends Model
{
    public string $id;
    public string $name;
    public bool $complete;
    public bool $expired;
    public bool $sent;
}
