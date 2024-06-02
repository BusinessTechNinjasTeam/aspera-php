<?php

namespace AsperaPHP;

use AsperaPHP\API\Model;

class User extends Model
{
    public string $id;
    public string $email;
    public string $first_name;
    public string $last_name;
}
