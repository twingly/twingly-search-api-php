<?php

require('vendor/autoload.php');

use Twingly\Client;

class SearchPostStream {
    public function __construct($keyword, $language = 'en')
    {
        $this->client = new Client(false, 'MyCompany/1.0');
        $this->query = $this->client->query();
        $this->query->search_query = sprintf('sort-order:asc sort:published lang:%s %s', $language, $keyword);
        $this->query->start_time = (new \DateTime('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P5D'));
    }

    public function each() {
        while(true) {
            $result = $this->query->execute();

            foreach($result->posts as $post) {
                yield $post;
            }

            if($result->all_results_returned()) {
                break;
            }
            $this->query->start_time = $result->posts[count($result->posts)-1]->published;
        }
    }
}

$stream = new SearchPostStream('(github) AND (hipchat OR slack)');
foreach($stream->each() as $post) {
    echo $post->url . "\n";
}
