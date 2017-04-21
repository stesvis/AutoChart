<?php

require __DIR__ . '/vendor/autoload.php';

$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://localhost:8000',
    'defaults' => [
        'exceptions' => false
    ]
]);

$vehicle = array(
    'year' => 2004,
    'make' => 'Renault',
    'model' => 'Clio',
);

$response = $client->post('/api/vehicles', [
    'body' => json_encode($vehicle)
]);

//$response = $client->get('/api/vehicles');

echo $response->getBody();
echo "\n\n";
