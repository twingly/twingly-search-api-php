<?php

require('vendor/autoload.php');

use Twingly\Client;

$client = new Client();
$query = $client->query();
$query->pattern = '"hello world"';
$query->start_time = (new \DateTime())->sub(new \DateInterval('P1D'));
$results = $query->execute();

foreach($results->posts as $post) {
    echo $post->url . "\n";
}