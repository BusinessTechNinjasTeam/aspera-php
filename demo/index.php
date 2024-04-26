<?php

$env = parse_ini_file(dirname( dirname(__FILE__) ) . '/.env');

$privateKey = file_get_contents(dirname( dirname(__FILE__) ) . '/private.pem');
$clientId = $env['CLIENT_ID'];
$userEmail = $env['USER_EMAIL'];
$orgSubdomain = $env['ORG_SUBDOMAIN'];
$clientSecret = $env['CLIENT_SECRET'];

$jwtHeader = [
    'typ' => 'JWT',
    'alg' => 'RS256'
];

$jwtBody = [
    'iss' => $clientId,
    'sub' => $userEmail,
    'aud' => 'https://api.asperafiles.com/api/v1/oauth2/token',
    'nbf' => time() - 3600,
    'exp' => time() + 3600,
];

$payload = sprintf(
    '%s.%s',
    base64_encode(json_encode($jwtHeader)),
    base64_encode(json_encode($jwtBody))
);

openssl_sign(
    $payload,
    $signature,
    $privateKey,
    OPENSSL_ALGO_SHA256
);

$jwt = sprintf('%s.%s', $payload, base64_encode($signature));

$filesUrl = "https://api.ibmaspera.com/api/v1/oauth2/$orgSubdomain/token";

$content = [
    'assertion' => $jwt,
    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
    'scope' => 'admin:all',
];

$ch = curl_init($filesUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['content_type' => 'application/x-www-form-urlencoded']);
curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$clientSecret");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$decoded_response = json_decode($response, true);

var_dump($decoded_response);
