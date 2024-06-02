<?php

namespace AsperaPHP\API;

class Workspace extends Model
{
    public string $id;
    public string $name;

    public string $home_file_id;
    public string $home_container_file_id;
}
