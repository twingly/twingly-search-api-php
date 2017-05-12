<?php

require('vendor/autoload.php');

use Twingly\Client;

$client = new Client();
$query = $client->query();
$query->search_query = '"hello world"';
$query->start_time = (new \DateTime('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P1D'));
$results = $query->execute();

foreach($results->posts as $post) {
    echo $post->url . "\n";
}
