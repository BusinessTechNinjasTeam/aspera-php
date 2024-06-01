<?php

use AsperaPHP\Auth\Actions\GetAccessToken;
use AsperaPHP\Auth\Config;

include './vendor/autoload.php';

/**
 * A mini-container for resolving classes that require environment data.
 */
function resolve($class, ...$args): mixed {

    $config = Config::fromArray(
        parse_ini_file('./.env')
    );
    $privateKey = file_get_contents('./private.pem');

    $resolved = match ($class) {
        GetAccessToken::class => new GetAccessToken($config, $privateKey),
    };

    return $args
        ? $resolved(...$args)
        : $resolved;
}

$request = (new \AsperaPHP\API\Request(
    resolve(GetAccessToken::class, 'user:all')
));

//$package = $request
//    ->list('packages', AsperaPHP\Package::class)
//    ->first();

$package = $request
    ->create('packages', \AsperaPHP\Package::class, [
        "name" => "Dev Test 202406010030",
        "note" => "Here are the files you asked for",
        "recipients" => [
            [
                "id" => "1166989",
                "type" => "user"
            ]
        ],
        "workspace_id" => "116127" // Ace-Netflix-Initiative
    ]);

var_dump($package);
