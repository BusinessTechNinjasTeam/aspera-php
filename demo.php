<?php

use AsperaPHP\API\Request;
use AsperaPHP\API\Workspace;
use AsperaPHP\Auth\Actions\GetAccessToken;
use AsperaPHP\Auth\Config;
use AsperaPHP\Node;
use AsperaPHP\Package;

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

$request = (new Request(
    resolve(GetAccessToken::class, 'admin:all')
));

$user = $request
    ->create('users', \AsperaPHP\User::class, [
       'first_name' => 'Test',
       'last_name' => 'Test',
       'email' => '202406012330@test.test',
    ]);
var_dump($user);

$request = (new Request(
    resolve(GetAccessToken::class, 'user:all')
));

$package = $request
    ->create('packages', \AsperaPHP\Package::class, [
        "name" => "Dev Test 202406012330",
        "note" => "This is a 202406012330 test.",
        "recipients" => [
            [
                "id" => $user->id,
                "type" => "user"
            ]
        ],
        "workspace_id" => "116127", // Ace-Netflix-Initiative
        "watermark" => "true",
    ]);
var_dump($package);

$node = $request
    ->fetch('nodes', \AsperaPHP\Node::class, $package->node_id);
var_dump($node);

$authorizationToken = resolve(GetAccessToken::class, "node.$node->access_key:user:all");

/** @var Package $package */
/** @var Node $node */
$transferRequest = new \AsperaPHP\API\TransferRequest($package, $node, $authorizationToken);

$transfer = $transferRequest->transfer(
    "193",
    "/ACE Netflix Initiative - Editor Stuart Bass Interview.mp4"
);

var_dump($transfer);

$request->update('packages', $package->id, [
    "sent" => true,
    "transfers_expected" => 1
]);
