<?php

use AsperaPHP\Aspera;
use AsperaPHP\Auth\Config;
use AsperaPHP\Node;
use AsperaPHP\Package;

include  dirname(__FILE__) . '/vendor/autoload.php';

$aspera = new Aspera(
    Config::fromArray(parse_ini_file( dirname(__FILE__) . '/.env')),
    $privateKey = file_get_contents(dirname(__FILE__) . '/private.pem')
);

/** ------------------------------------------------------------
 * WORKFLOW:
 *  - CREATE A USER
 */

$request = $aspera->requestWithScope('admin:all');

$user = $request
    ->create('users', \AsperaPHP\User::class, [
//       'first_name' => 'Test',
//       'last_name' => 'Test',
//       'email' => '202406012330@test.test',
        'email' => $recipientEmail,
    ]);
ray($user);

/** ------------------------------------------------------------
 * WORKFLOW:
 *  - CREATE A PACKAGE
 *  - FETCH THE RELATED NODE
 *  - TRANSFER A FILE TO THAT PACKAGE
 */

$request = $aspera->requestWithScope('user:all');

$package = $request
    ->create('packages', \AsperaPHP\Package::class, [
        "name" => "Transfer for User #{$user->id}",
        "note" => "Transfer for User #{$user->id}",
        "recipients" => [
            [
                "id" => $user->id,
                "type" => "user"
            ]
        ],
        "workspace_id" => "116127", // Ace-Netflix-Initiative
        "watermark" => "true",
    ]);
ray($package);

$node = $request->fetch('nodes', \AsperaPHP\Node::class, $package->node_id);

/** @var Package $package */
/** @var Node $node */
$transferRequest = $aspera->transferRequestForPackage($package, $node);

$transfer = $transferRequest->transfer(
    "193",
    "/ACE Netflix Initiative - Editor Stuart Bass Interview.mp4"
);
ray($transfer);

$request->update('packages', $package->id, [
    "sent" => true,
    "transfers_expected" => 1
]);
