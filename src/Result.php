<?php

namespace Twingly;

/**
 * Represents a result from a Query to the Search API
 *
 * @package Twingly
 */
class Result {
    /**
     * @var int number of posts the query returned
     */
    public $number_of_matches_returned = 0;
    /**
     * @var float number of seconds it took to execute the query
     */
    public $seconds_elapsed = 0.0;
    /**
     * @var int total number of posts the query matched
     */
    public $number_of_matches_total = 0;
    /**
     * @var array all posts that matched the query
     */
    public $posts = [];

    /**
     * @return bool returns TRUE if this result includes all Posts that matched the query
     */
    public function all_results_returned() {
        return $this->number_of_matches_returned == $this->number_of_matches_total;
    }

    public function __toString()
    {
        $matches = sprintf("posts, number_of_matches_returned=%d, number_of_matches_total=%d",
            $this->number_of_matches_returned, $this->number_of_matches_total);

        return sprintf("#<%s:0x%s %s>", get_class($this), spl_object_hash($this), $matches);
    }
}
